<?php

namespace WebDev\LatvijasPasts\Model;

use Magento\Catalog\Model\ProductRepository;
use Magento\Directory\Model\ResourceModel\Region\Collection;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use GuzzleHttp\ClientFactory;
use Magento\Framework\Webapi\Rest\Request;
use GuzzleHttp\Psr7\ResponseFactory;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Backend\App\ConfigInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;
use WebDev\LatvijasPasts\Api\CountryManagementInterface;
use WebDev\LatvijasPasts\Api\UsaStateManagementInterface;

class ExpressPastsParcelCreate
{
    private const API_REQUEST_URI = 'https://express.pasts.lv/';
    private const API_REQUEST_ENDPOINT = 'parcelsApi/create';
    private const SECRET_KEY_PATH = 'carriers/latvijaspasts/express_secret';
    private const DEFAULT_HS_CODE_PATH = 'carriers/latvijaspasts/hs_code';
    private const DEFAULT_ORIGIN_COUNTRY_PATH = 'carriers/latvijaspasts/origin_country';

    private const MULTIPLE_PARCELS = 0;
    private const POST_PAYMENT = 0;
    private const DOCUMENTS_RETURN = 0;
    private const SMS_INVITE = 1;
    private const DELIVERY_PRIORITIES = ['X1', 'X2'];
    private const EU_COUNTRIES = [
        'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI',
        'FR', 'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU',
        'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE',
    ];

    private Carrier $carrier;
    private CountryManagementInterface $countryManagement;
    private UsaStateManagementInterface $usaStateManagement;
    private Collection $regionCollection;
    private ProductRepository $productRepository;
    private ClientFactory $clientFactory;
    private ResponseFactory $responseFactory;
    private ConfigInterface $backendConfig;
    private LoggerInterface $logger;
    private ProductMetadataInterface $productMetadata;

    public function __construct(
        Carrier                     $carrier,
        CountryManagementInterface  $countryManagement,
        UsaStateManagementInterface $usaStateManagement,
        Collection                  $regionCollection,
        ProductRepository           $productRepository,
        ClientFactory               $clientFactory,
        ResponseFactory             $responseFactory,
        ConfigInterface             $backendConfig,
        LoggerInterface             $logger,
        ProductMetadataInterface    $productMetadata,
    )
    {
        $this->carrier = $carrier;
        $this->countryManagement = $countryManagement;
        $this->usaStateManagement = $usaStateManagement;
        $this->regionCollection = $regionCollection;
        $this->productRepository = $productRepository;
        $this->clientFactory = $clientFactory;
        $this->responseFactory = $responseFactory;
        $this->backendConfig = $backendConfig;
        $this->logger = $logger;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @throws LocalizedException
     */
    public function request(
        OrderInterface $order = null,
        array          $package = null,
        string         $packagePriority = null,
        string         $packageSize = null
    )
    {
        if (!$order || !$package) {
            return null;
        }
        if (!in_array($packagePriority, self::DELIVERY_PRIORITIES)) {
            throw new LocalizedException(
                new Phrase(__('Invalid package priority: %1!'), [$packagePriority])
            );
        }
        $packageWeight = $package['params']['weight'] ?? null;
        $packageLength = $package['params']['length'];
        $packageWidth = $package['params']['width'];
        $packageHeight = $package['params']['height'];
        $arePackageDimensionsSet = $packageLength || $packageWidth || $packageHeight;
        if ($arePackageDimensionsSet && (!$packageLength || !$packageWidth || !$packageHeight)) {
            throw new LocalizedException(
                new Phrase(__('Invalid package dimensions!'))
            );
        } else {
            $packageLength = (float)$packageLength / 100;
            $packageWidth = (float)$packageWidth / 100;
            $packageHeight = (float)$packageHeight / 100;
        }

        $shippingAddress = $order->getShippingAddress();
        $shippingMethod = $order->getShippingMethod();
        $latvijasPastsJson = $shippingAddress->getLatvijasPastsJson();
        $orderIncrementId = $order->getIncrementId();
        $countryCode = $order->getShippingAddress()->getCountryId();
        $countryId = $this->getCountryId($countryCode);
        $shippingType = match ($countryCode) {
            'LV' => 'Ie',
            'LT', 'EE' => 'Be',
            default => 'Ems',
        };

        $commonConfig = [
            'data_source' => 'Magento WebDev',
            'data_version' => $this->productMetadata->getVersion(),
            'type' => $shippingType,
            'package_weight' => $packageWeight,
            'delivery_priority' => $packagePriority,
            'country_id' => $countryId,
            'name_surname' => $order->getShippingAddress()->getName(),
            'company_name' => $order->getShippingAddress()->getCompany(),
            'phone' => $this->removeNonNumeric($order->getShippingAddress()->getTelephone()),
            'email' => $order->getShippingAddress()->getEmail(),
            'multiparcel' => self::MULTIPLE_PARCELS,
            'post_payment' => self::POST_PAYMENT,
            'documents_return' => self::DOCUMENTS_RETURN,
        ];

        if ($arePackageDimensionsSet) {
            $commonConfig['width'] = $packageWidth;
            $commonConfig['height'] = $packageHeight;
            $commonConfig['length'] = $packageLength;
        }

        switch ($shippingMethod) {
            case $this->carrier->getPostOfficeMethodCodeFull():
                $pickupPointData = json_decode($latvijasPastsJson, true);
                $config = [
                    'pickup' => 1,
                    'city' => $pickupPointData['city'],
                    'area' => $pickupPointData['area'],
                    'district' => $pickupPointData['district'],
                    'village' => $pickupPointData['village'],
                    'street' => $pickupPointData['street'],
                    'house' => $pickupPointData['house'],
                    'zipcode' => $this->validateLativaZip($pickupPointData['zipcode']),
                    'pickup_zipcode' => $this->validateLativaZip($pickupPointData['zipcode']),
                ];
                break;
            case $this->carrier->getPostOfficeLithuaniaMethodCodeFull():
                $pickupPointData = json_decode($latvijasPastsJson, true);
                $config = $this->processFullAddress($pickupPointData['address']);
                $config = array_replace($config, [
                    'pickup' => 1,
                    'pickup_zipcode' => $pickupPointData['code'],
                    'area' => $pickupPointData['area'],
                    'district' => $pickupPointData['district'],
                    'village' => $pickupPointData['village'],
                    'street' => $pickupPointData['street'],
                    'house' => $pickupPointData['house'],
                    'zipcode' => $this->removeNonNumeric($pickupPointData['zipcode']),
                ]);
                break;
            case $this->carrier->getLockerMethodCodeFull():
                $pickupPointData = json_decode($latvijasPastsJson, true);
                $config = [
                    'station_id' => $pickupPointData['id'],
                    'zipcode' => $this->validateLativaZip($pickupPointData['zipcode']),
                ];
                break;
            case $this->carrier->getLockerLithuaniaMethodCodeFull():
                $pickupPointData = json_decode($latvijasPastsJson, true);
                $config = $this->processFullAddress($pickupPointData['address']);
                $config = array_replace($config, [
                    'terminal_id' => $pickupPointData['id'],
                    'size' => $packageSize,
                    'zipcode' => $this->removeNonNumeric($pickupPointData['postal_code']),
                ]);
                break;
            case $this->carrier->getCircleKDusMethodCodeFull():
                $pickupPointData = json_decode($latvijasPastsJson, true);
                $config = [
                    'statoil_id' => $pickupPointData['id'],
                    'sms_invite' => self::SMS_INVITE,
                    'zipcode' => $this->validateLativaZip($pickupPointData['zipcode']),
                ];
                break;
            case $this->carrier->getExpressMethodCodeFull():
                $zipcode = $countryCode === 'LV'
                    ? $this->validateLativaZip($shippingAddress->getData('postcode'))
                    : preg_replace('/[^0-9]/', '', $shippingAddress->getData('postcode'));

                $config = [
                    'city' => $shippingAddress->getData('city'),
                    'area' => $shippingAddress->getData('region'),
                    'zipcode' => $zipcode,
                ];

                $address = explode("\n", $shippingAddress->getData('street'));
                if (isset($address[1])) {
                    $config['comments'] = $address[1];
                }
                $addressLine1 = explode(' ', $address[0]);
                if (count($addressLine1) >= 2) {
                    $house = end($addressLine1);
                    if (preg_replace('/[^0-9]/', '', $house)) {
                        $config['house'] = $house;
                        array_pop($addressLine1);
                    }
                }
                $config['street'] = implode(' ', $addressLine1);

                if ($countryCode === 'US') {
                    $usaStateId = $shippingAddress->getData('region_id');
                    $foundUsaState = $this->regionCollection
                        ->addFieldToFilter('main_table.region_id', ['eq' => $usaStateId])
                        ->getFirstItem();
                    $usaStateCode = $foundUsaState->getData('code');
                    $usaStateName = $foundUsaState->getData('default_name');
                    if (!$this->validateUsaState($usaStateCode, $usaStateName)) {
                        throw new LocalizedException(
                            new Phrase(__('US state (%1) not found or not supported by ExpressPasts Courier!'), [$usaStateCode])
                        );
                    }
                    $config['zip_state'] = $usaStateCode;
                }

                if (($countryCode === 'US' || $countryCode === 'RU') && empty($config['house'])) {
                    throw new LocalizedException(
                        new Phrase(__('House of the address is required for shipments to US or Russia!'))
                    );
                }

                $isEuCountry = in_array($countryCode, self::EU_COUNTRIES);
                if ($countryCode !== 'LV' && !$isEuCountry) {
                    $defaultHsCode = $this->backendConfig->getValue(self::DEFAULT_HS_CODE_PATH);
                    $defaultOriginCountryCode = $this->backendConfig->getValue(self::DEFAULT_ORIGIN_COUNTRY_PATH);
                }

                $items = [];
                foreach ($order->getAllVisibleItems() as $orderItem) {
                    $itemName = $orderItem['name'];
                    $amount = (int)round($orderItem['qty_ordered']);
                    if ($amount != $orderItem['qty_ordered']) {
                        throw new LocalizedException(
                            new Phrase(
                                __('Item quantity can only be an integer number! Found quantity of %1 for %2'),
                                [$orderItem['qty_ordered'], $itemName]
                            )
                        );
                    }
                    $item = [
                        'title' => $itemName,
                        'amount' => $amount,
                        'weight' => (float)$orderItem['weight'] ?: 0.001,
                        'value' => (float)$orderItem['price'],
                    ];

                    if (!$isEuCountry) {
                        if (!isset($defaultHsCode) || !isset($defaultOriginCountryCode)) {
                            throw new LocalizedException(
                                new Phrase(__('Default origin country or HS code not found!'))
                            );
                        }
                        $product = $this->productRepository->getById($orderItem['item_id']);
                        $originCountryCode = $product->getData('country_of_origin') ?: $defaultOriginCountryCode;
                        $originCountryId = $this->getCountryId($originCountryCode);
                        if (!$originCountryId) {
                            throw new LocalizedException(
                                new Phrase(__('Origin country (%1) not found or not supported by ExpressPasts Courier!'), [$originCountryCode])
                            );
                        }
                        $item['hs_code'] = $product->getData('harmonisation_code') ?: $defaultHsCode;
                        $item['origin_country_id'] = $originCountryId;
                    }
                    $items[] = $item;
                }

                $config['package_contents'] = 'Prece';
                $config['ParcelContent'] = $items;
                $config['content_currency'] = $order->getOrderCurrencyCode();
                break;
            default:
                $config = null;
        }

        if (empty($config)) {
            return null;
        }

        $config = array_filter(array_merge($commonConfig, $config));

        $parcelId = $orderIncrementId . '_' . time();
        $requestParams = [
            'secret' => $this->backendConfig->getValue(self::SECRET_KEY_PATH),
            'parcels' => [
                $parcelId => $config,
            ],
        ];

        $response = $this->doRequest($requestParams);

        $status = $response->getStatusCode();
        if ($status !== 200) {
            $message = new Phrase(__('Could not create shipment for order ID %1. Response code: %2'), [$orderIncrementId, $status]);
            $this->logger->critical($message);
            throw new LocalizedException($message);
        }
        $responseBody = $response->getBody();
        $responseContent = $responseBody->getContents();

        $responseArray = json_decode($responseContent, true);

        if (!count($responseArray) || !isset($responseArray[$parcelId]) || is_array($responseArray[$parcelId])) {
            $message = new Phrase(
                __('Could not create shipment for order ID %1. Response received: %2'),
                [$orderIncrementId, $this->arrayToString($responseArray)]
            );
            $this->logger->critical($message);
            throw new LocalizedException($message);
        }

        return $responseArray[$parcelId];
    }

    public function doRequest($params)
    {
        $client = $this->clientFactory->create(['config' => [
            'base_uri' => self::API_REQUEST_URI
        ]]);

        try {
            $response = $client->request(
                Request::HTTP_METHOD_POST,
                self::API_REQUEST_ENDPOINT,
                [
                    'form_params' => $params,
                ]
            );
        } catch (GuzzleException $exception) {
            $response = $this->responseFactory->create([
                'status' => $exception->getCode(),
                'reason' => $exception->getMessage()
            ]);
        }

        return $response;
    }

    private function validateLativaZip(string $zip)
    {
        $zip = trim($zip);
        if (!str_starts_with($zip, 'LV-')) {
            $zip = 'LV-' . $zip;
        }
        return $zip;
    }

    private function removeNonNumeric(string $phone)
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    private function validateUsaState(string $usaStateCode = null, string $usaStateName = null)
    {
        if (!$usaStateCode) {
            return false;
        }
        $validUsaStates = $this->usaStateManagement->fetch();
        if (array_key_exists($usaStateCode, $validUsaStates)) {
            return true;
        }
        if (in_array($usaStateName, $validUsaStates)) {
            return true;
        }
        return false;
    }

    private function getCountryId(string $countryCode = null)
    {
        if (!$countryCode) {
            return false;
        }
        $expressCountries = $this->countryManagement->fetch();
        $foundCountryKey = array_search($countryCode, array_column($expressCountries, 'iso2'));
        if ($foundCountryKey === false) {
            return false;
        }
        return $expressCountries[$foundCountryKey]['id'] ?? false;
    }

    private function arrayToString(array $json)
    {
        $result = [];
        array_walk_recursive($json, function ($value, $key) use (&$result) {
            $result[] = $key . ': ' . $value;
        });
        return implode(' ', $result);
    }

    private function processFullAddress(string $fullAddress = null): array
    {
        $location = [];
        $address = explode(',', $fullAddress);

        if (count($address) > 1) {
            $postCodeAndCity = end($address);
            array_pop($address);

            preg_match('/(\d{5}|\d{4})/', $postCodeAndCity, $zipcode);
            if (empty($zipcode)) {
                $city = trim($postCodeAndCity);
            } else {
                $zipcode = $zipcode[0];
                $location['zipcode'] = $zipcode;
                $city = trim(str_replace($zipcode, '', $postCodeAndCity));
            }
            $location['city'] = $city;
        } elseif (count($address) === 1) {
            $address = $address[0];
            preg_match('/(\d{5}|\d{4})/', $address, $zipcode);
            if (!empty($zipcode)) {
                $zipcode = $zipcode[0];
                $location['zipcode'] = $zipcode;
                $address = trim(str_replace($zipcode, '', $address));
            }
        } else {
            return $location;
        }

        $address = explode(' ', is_array($address) ? $address[0] : $address);
        if (count($address) < 2) {
            $location['street'] = implode(' ', $address);
            return $location;
        }
        $house = end($address);
        if (preg_replace('/[^0-9]/', '', $house)) {
            $location['house'] = $house;
            array_pop($address);
        }
        $location['street'] = implode(' ', $address);
        return $location;
    }
}
