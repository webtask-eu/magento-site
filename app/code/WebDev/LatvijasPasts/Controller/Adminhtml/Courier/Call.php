<?php

namespace WebDev\LatvijasPasts\Controller\Adminhtml\Courier;

use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\ConfigInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Webapi\Rest\Request;

class Call extends Action implements HttpGetActionInterface
{
    private const API_REQUEST_URI = 'https://express.pasts.lv/';
    private const API_REQUEST_ENDPOINT = 'courierApplicationsApi/create';
    private const SECRET_KEY_PATH = 'carriers/latvijaspasts/express_secret';

    private ConfigInterface $backendConfig;
    private ClientFactory $clientFactory;
    private ResponseFactory $responseFactory;
    private RawFactory $rawFactory;

    public function __construct(
        Context         $context,
        ConfigInterface $backendConfig,
        ClientFactory   $clientFactory,
        ResponseFactory $responseFactory,
        RawFactory      $rawFactory
    )
    {
        parent::__construct($context);
        $this->backendConfig = $backendConfig;
        $this->clientFactory = $clientFactory;
        $this->responseFactory = $responseFactory;
        $this->rawFactory = $rawFactory;
    }

    /**
     * Call ExpressPast Courier
     */
    public function execute()
    {
        $request = $this->getRequest();
        $courierApplication = $request->getQuery()->toArray();
        $requestId = (string)time();

        $request = [
            'secret' => $this->backendConfig->getValue(self::SECRET_KEY_PATH),
            'applications' => [
                $requestId => (object)$courierApplication,
            ]
        ];

        $response = $this->doRequest($request);
        $response = json_decode($response->getBody());
        $result = $this->rawFactory->create();

        if (!isset($response->result->{$requestId}) || isset($response->error)) {
            $result->setContents($response->error ?? 'Failed to create a courier request!');
            return $result;
        }

        $responseMessage = $response->result->{$requestId};
        if (!is_bool($responseMessage) && !is_int($responseMessage)) {
            if (is_string($responseMessage)) {
                $result->setContents($responseMessage);
            } else {
                $fullMessage = '';
                foreach ($responseMessage as $fieldName => $messageItem) {
                    $fieldName = str_replace('_', '', ucwords($fieldName, '_'));
                    $fullMessage .= "$fieldName: $messageItem<br>";
                }
                $result->setContents($fullMessage);
            }
            return $result;
        }

        $result->setContents(__('Courier call successful!'));
        return $result;
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
}
