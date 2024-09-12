<?php

namespace WebDev\LatvijasPasts\Model;

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Config\ScopeConfigInterface;
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

class MansPastsParcelCreate
{
    private const REQUEST_URI = 'https://manspasts.lv/';
    private const REQUEST_URI_DEMO = 'https://apidemo.manspasts.lv/';
    private const REQUEST_ENDPOINT = 'api/packages';

    private const USE_DEMO_PATH = 'carriers/latvijaspasts/mans_pasts_demo';
    private const USER_ID_PATH = 'carriers/latvijaspasts/mans_pasts_user';
    private const API_KEY_PATH = 'carriers/latvijaspasts/mans_pasts_secret';
    private const ITEM_TYPES = ['Parcel', 'Letter'];
    private const POSTAGE_TYPES = ['Insured', 'Ordinary', 'Registered', 'Tracked'];
    private const EU_COUNTRIES = [
        'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI',
        'FR', 'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU',
        'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE',
    ];

    private Carrier $carrier;
    private ProductRepository $productRepository;
    private ScopeConfigInterface $scopeConfig;
    private ClientFactory $clientFactory;
    private ResponseFactory $responseFactory;
    private ConfigInterface $backendConfig;
    private LoggerInterface $logger;
    private ProductMetadataInterface $productMetadata;

    public function __construct(
        Carrier                  $carrier,
        ProductRepository        $productRepository,
        ScopeConfigInterface     $scopeConfig,
        ClientFactory            $clientFactory,
        ResponseFactory          $responseFactory,
        ConfigInterface          $backendConfig,
        LoggerInterface          $logger,
        ProductMetadataInterface $productMetadata,
    )
    {
        $this->carrier = $carrier;
        $this->productRepository = $productRepository;
        $this->scopeConfig = $scopeConfig;
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
        string         $packageType = null
    )
    {
        if (!$order || !$package || !$packageType) {
            return null;
        }
        $packageWeight = $package['params']['weight'] ?? null;

        $shippingAddress = $order->getShippingAddress();
        $shippingMethod = $order->getShippingMethod();
        $orderIncrementId = $order->getIncrementId();

        if ($shippingMethod !== $this->carrier->getMansPastsMethodCodeFull()) {
            return null;
        }

        $itemAndPostageType = explode('_', $packageType);
        $itemType = $itemAndPostageType[0];
        $postageType = $itemAndPostageType[1];
        if (!in_array($itemType, self::ITEM_TYPES) || !in_array($postageType, self::POSTAGE_TYPES)) {
            throw new LocalizedException(
                new Phrase(
                    __('Invalid package type: %1!'),
                    [__(str_replace('_', ' - ', $itemAndPostageType))]
                )
            );
        }

        $countryCode = $shippingAddress->getCountryId();
        $addressLine1 = $shippingAddress->getStreet()[0] ?? '';
        $addressLine2 = $shippingAddress->getCity();
        if ($countryCode === 'US') {
            $addressLine2 .= ', ' . $shippingAddress->getRegionCode();
        }
        $customerName = $shippingAddress->getFirstname();
        if ($shippingAddress->getMiddlename()) {
            $customerName .= ' ' . $shippingAddress->getMiddlename();
        }
        $customerName .= ' ' . $shippingAddress->getLastname();

        $address = [
            'countryCode' => $countryCode,
            'freeformAddressLine1' => $addressLine1,
            'freeformAddressLine2' => $addressLine2,
            'name' => $customerName,
        ];

        $address['postCode'] = $countryCode === 'LV'
            ? $shippingAddress->getPostcode()
            : preg_replace('/[^0-9]/', '', $shippingAddress->getPostcode());

        $address['phone'] = $shippingAddress->getTelephone();

        if ($shippingAddress->getEmail()) {
            $address['email'] = $shippingAddress->getEmail();
        }

        if (in_array($countryCode, self::EU_COUNTRIES)) {
            if ($countryCode !== 'LV') {
                $address['userPackageWeight'] = (float)$packageWeight;
            }
        } else {
            $defaultHsCode = $this->scopeConfig->getValue('carriers/latvijaspasts/hs_code');
            $defaultOriginCountry = $this->scopeConfig->getValue('carriers/latvijaspasts/origin_country');
            if (!$defaultHsCode || !$defaultOriginCountry) {
                throw new LocalizedException(
                    new Phrase(__('Default origin country or HS code not found!'))
                );
            }

            $items = [];
            foreach ($order->getAllVisibleItems() as $orderItem) {
                $product = $this->productRepository->getById($orderItem->getItemId());
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
                $items[] = [
                    'name' => $orderItem->getName(),
                    'quantity' => $amount,
                    'weight' => $orderItem['weight']
                        ? (int)round($orderItem['weight'] * 1000)
                        : 1,
                    'value' => (float)$orderItem->getPrice(),
                    'hsCode' => $product->getData('harmonisation_code') ?: $defaultHsCode,
                    'country' => $product->getData('country_of_origin') ?: $defaultOriginCountry,
                ];
            }
            $address['contentItems'] = $items;
            $address['contentType'] = 'Other';
        }

        $request = [
            'package_create' => [
                'user' => $this->backendConfig->getValue(self::USER_ID_PATH),
                'api_key' => $this->backendConfig->getValue(self::API_KEY_PATH),
                'source' => 'Magento WebDev',
                'version' => $this->productMetadata->getVersion(),
                'type' => 'Goods',
                'postageType' => $postageType,
                'itemType' => $itemType,
                'addresses' => [$address]
            ]
        ];

        $response = $this->doRequest($request);

        $status = $response->getStatusCode();
        if ($status !== 200) {
            $message = new Phrase(
                __('Could not create shipment for order ID %1. Response code: %2'),
                [$orderIncrementId, $status]
            );
            $this->logger->critical($message);
            throw new LocalizedException($message);
        }
        $responseContent = $response->getBody()->getContents();

        $response = json_decode($responseContent, true);

        if (!isset($response['barcodes'])) {
            $message = new Phrase(
                __('Could not create shipment for order ID %1. Response received: %2'),
                [$orderIncrementId, $responseContent]
            );
            $this->logger->critical($message);
            throw new LocalizedException($message);
        }

        return $response['barcodes'][0] ?? false;
    }

    public function doRequest(array $options)
    {
        $baseUri = $this->backendConfig->getValue(self::USE_DEMO_PATH)
            ? self::REQUEST_URI_DEMO
            : self::REQUEST_URI;

        $client = $this->clientFactory->create(['config' => [
            'headers' => ['Content-Type' => 'application/json'],
            'base_uri' => $baseUri,
        ]]);

        try {
            $response = $client->request(
                Request::HTTP_METHOD_POST,
                self::REQUEST_ENDPOINT,
                [
                    'body' => json_encode($options)
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
}
