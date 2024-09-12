<?php

namespace WebDev\LatvijasPasts\Model;

use GuzzleHttp\ClientFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Webapi\Rest\Request;
use GuzzleHttp\Psr7\ResponseFactory;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Backend\App\ConfigInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;

class MansPastsShippingLabel
{
    private const REQUEST_URI = 'https://manspasts.lv/';
    private const REQUEST_URI_DEMO = 'https://apidemo.manspasts.lv/';
    private const REQUEST_ENDPOINT = 'api/documents';

    private const LABEL_TYPE = 'Addressee';
    private const LABEL_LAYOUT = '3x8';
    private const LABEL_DIMENSIONS = '70x35';
    private const LABEL_COLUMN = 1;
    private const LABEL_ROW = 1;
    private const USE_DEMO_PATH = 'carriers/latvijaspasts/mans_pasts_demo';
    private const USER_ID_PATH = 'carriers/latvijaspasts/mans_pasts_user';
    private const API_KEY_PATH = 'carriers/latvijaspasts/mans_pasts_secret';
    private const EU_COUNTRIES = [
        'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI',
        'FR', 'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU',
        'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE',
    ];

    private ClientFactory $clientFactory;
    private ResponseFactory $responseFactory;
    private ConfigInterface $backendConfig;
    private LoggerInterface $logger;

    public function __construct(
        ClientFactory   $clientFactory,
        ResponseFactory $responseFactory,
        ConfigInterface $backendConfig,
        LoggerInterface $logger
    )
    {
        $this->clientFactory = $clientFactory;
        $this->responseFactory = $responseFactory;
        $this->backendConfig = $backendConfig;
        $this->logger = $logger;
    }

    /**
     * @throws LocalizedException
     */
    public function requestLabel(OrderInterface $order, $trackingNumber = null)
    {
        if (!$trackingNumber) {
            return null;
        }

        $countryCode = $order->getShippingAddress()->getCountryId();

        $document = [
            'user' => $this->backendConfig->getValue(self::USER_ID_PATH),
            'api_key' => $this->backendConfig->getValue(self::API_KEY_PATH),
            'barcode' => $trackingNumber,
            'documentType' => $countryCode === 'LV'
                ? 'Address labels'
                : 'Accompanying documents',
            'addressLabelType' => self::LABEL_TYPE,
            'addressLabelLayout' => self::LABEL_LAYOUT,
            'addressLabelDimensions' => self::LABEL_DIMENSIONS,
            'addressLabelInitialColumn' => self::LABEL_COLUMN,
            'addressLabelInitialRow' => self::LABEL_ROW,
        ];

        if (!in_array($countryCode, self::EU_COUNTRIES)) {
            $document['documentPrintType'] = 'A4';
        }

        $options = ['document' => $document];

        $response = $this->doRequest($options);

        $status = $response->getStatusCode();
        if ($status != 200) {
            $message = new Phrase(
                __('Could not create label for order ID %1. Response code: %2'),
                [$trackingNumber, $status]
            );
            $this->logger->critical($message);
            throw new LocalizedException($message);
        }

        $response = json_decode($response->getBody());
        if (!isset($response->link)) {
            $responseError = $response->message ?? 'Unknown error message';
            $message = new Phrase(__('Could not get label URL for order ID %1. Response received: %2'), [$trackingNumber, $responseError]);
            $this->logger->critical($message);
            throw new LocalizedException($message);
        }

        return file_get_contents($response->link);
    }

    public function doRequest(array $options)
    {
        $baseUri = $this->backendConfig->getValue(self::USE_DEMO_PATH)
            ? self::REQUEST_URI_DEMO
            : self::REQUEST_URI;

        $client = $this->clientFactory->create(['config' => [
            'headers' => ['Content-Type' => 'application/json'],
            'base_uri' => $baseUri
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
