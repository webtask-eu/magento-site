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

class ExpressPastsShippingLabel
{

    private const API_REQUEST_URI = 'https://express.pasts.lv/';
    private const STICKER_API_REQUEST_ENDPOINT = 'parcelsDocumentsApi/stickers';
    private const LABEL_API_REQUEST_ENDPOINT = 'parcelsDocumentsApi/labels';

    private const LABEL_SIZE = '150x100';

    private const SECRET_KEY_PATH = 'carriers/latvijaspasts/express_secret';

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
        if (in_array($countryCode, ['LV', 'LT'])) {
            $requestParams = [
                'secret' => $this->backendConfig->getValue(self::SECRET_KEY_PATH),
                'size' => self::LABEL_SIZE,
                'parcels' => [$trackingNumber],
            ];
            $endpoint = self::STICKER_API_REQUEST_ENDPOINT;
        } else {
            $requestParams = [
                'secret' => $this->backendConfig->getValue(self::SECRET_KEY_PATH),
                'parcels' => [$trackingNumber],
            ];
            $endpoint = self::LABEL_API_REQUEST_ENDPOINT;
        }

        $response = $this->doRequest($requestParams, $endpoint);

        $status = $response->getStatusCode();

        if ($status != 200) {
            $message = new Phrase(
                __('Could not create label for order ID %1. Response code: %2'),
                [$trackingNumber, $status]
            );
            $this->logger->critical($message);
            throw new LocalizedException($message);
        }

        return $response->getBody()->getContents();
    }

    public function doRequest(array $params, $endpoint)
    {
        $client = $this->clientFactory->create(['config' => [
            'base_uri' => self::API_REQUEST_URI
        ]]);

        try {
            $response = $client->request(
                Request::HTTP_METHOD_POST,
                $endpoint,
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
}
