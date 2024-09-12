<?php

namespace WebDev\LatvijasPasts\Controller\Adminhtml\Manifest;

use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\ConfigInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Model\ResourceModel\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class Printmanspasts extends Action implements HttpGetActionInterface
{
    private const API_REQUEST_URI = 'https://manspasts.lv/';
    private const API_REQUEST_URI_DEMO = 'https://apidemo.manspasts.lv/';
    private const API_REQUEST_ENDPOINT = 'api/combine-ps27';
    private const USE_DEMO_PATH = 'carriers/latvijaspasts/mans_pasts_demo';
    private const USER_ID_PATH = 'carriers/latvijaspasts/mans_pasts_user';
    private const API_KEY_PATH = 'carriers/latvijaspasts/mans_pasts_secret';

    private Order $orderResourceModel;
    private ConfigInterface $backendConfig;
    private ClientFactory $clientFactory;
    private ResponseFactory $responseFactory;
    private CollectionFactory $orderCollectionFactory;
    private RawFactory $rawFactory;

    public function __construct(
        Context           $context,
        Order             $orderResourceModel,
        ConfigInterface   $backendConfig,
        ClientFactory     $clientFactory,
        ResponseFactory   $responseFactory,
        CollectionFactory $orderCollectionFactory,
        RawFactory        $rawFactory,
    )
    {
        parent::__construct($context);
        $this->orderResourceModel = $orderResourceModel;
        $this->backendConfig = $backendConfig;
        $this->clientFactory = $clientFactory;
        $this->responseFactory = $responseFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->rawFactory = $rawFactory;
    }

    /**
     * Get MansPasts Manifest
     * @throws LocalizedException
     */
    public function execute()
    {
        $order_ids = $this->getRequest()->getQuery('order_ids');
        $orders = $this->orderCollectionFactory
            ->create()
            ->addFieldToFilter('entity_id', ['in' => $order_ids])
            ->load();

        $barcodes = [];
        foreach ($orders as $order) {
            $manifestUrl = $order->getManifestUrl();
            if (isset($manifestUrl)) {
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                return $resultRedirect->setPath($manifestUrl);
            }
            $tracksCollection = $order->getTracksCollection();
            foreach ($tracksCollection->getItems() as $track) {
                $barcodes[] = $track->getTrackNumber();
            }
        }

        if (empty($barcodes)) {
            $result = $this->rawFactory->create();
            $result->setContents(
                __('No shipments found in any of the orders!') . '<br>' .
                __('Create a shipping label for each order!') . '<br>' .
                '<button onclick="history.back()">' . __('Go Back') . '</button>'
            );
            return $result;
        }

        $document = [
            'user' => $this->backendConfig->getValue(self::USER_ID_PATH),
            'api_key' => $this->backendConfig->getValue(self::API_KEY_PATH),
            'barcodes' => $barcodes,
        ];
        $request = ["combine_ps27" => (object)$document];

        $response = $this->doRequest($request);

        $response = json_decode($response->getBody());

        if (!isset($response->link)) {
            if (isset($response->message)) {
                $responseMessage = (new \ArrayIterator($response->message))->current();
            }
            throw new LocalizedException(
                new Phrase($responseMessage ?? __('Could not get the manifest URL!'))
            );
        }

        $date = date('Y-m-d H:i:s');
        foreach ($orders as $order) {
            $order->setManifestDate($date);
            $order->setManifestUrl($response->link);
            $this->orderResourceModel->save($order);
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath($response->link);
    }

    public function doRequest(array $options)
    {
        $baseUri = $this->backendConfig->getValue(self::USE_DEMO_PATH)
            ? self::API_REQUEST_URI_DEMO
            : self::API_REQUEST_URI;

        $client = $this->clientFactory->create(['config' => [
            'headers' => ['Content-Type' => 'application/json'],
            'base_uri' => $baseUri,
        ]]);

        try {
            $response = $client->request(
                Request::HTTP_METHOD_POST,
                self::API_REQUEST_ENDPOINT,
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
