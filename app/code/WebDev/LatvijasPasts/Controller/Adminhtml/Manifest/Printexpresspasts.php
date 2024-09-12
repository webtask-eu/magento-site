<?php

namespace WebDev\LatvijasPasts\Controller\Adminhtml\Manifest;

use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\ConfigInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Model\ResourceModel\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class Printexpresspasts extends Action implements HttpGetActionInterface
{
    private const REQUEST_URI = 'https://express.pasts.lv/';
    private const REQUEST_ENDPOINT = 'parcelsDocumentsApi/manifest';
    private const SECRET_KEY_PATH = 'carriers/latvijaspasts/express_secret';

    private Order $orderResourceModel;
    private ConfigInterface $backendConfig;
    private ClientFactory $clientFactory;
    private ResponseFactory $responseFactory;
    private CollectionFactory $orderCollectionFactory;
    private RawFactory $rawFactory;
    private FileFactory $fileFactory;

    public function __construct(
        Context           $context,
        Order             $orderResourceModel,
        ConfigInterface   $backendConfig,
        ClientFactory     $clientFactory,
        ResponseFactory   $responseFactory,
        CollectionFactory $orderCollectionFactory,
        RawFactory        $rawFactory,
        FileFactory       $fileFactory,
    )
    {
        parent::__construct($context);
        $this->orderResourceModel = $orderResourceModel;
        $this->backendConfig = $backendConfig;
        $this->clientFactory = $clientFactory;
        $this->responseFactory = $responseFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->rawFactory = $rawFactory;
        $this->fileFactory = $fileFactory;
    }

    /**
     * Get ExpressPasts Manifest
     * @throws LocalizedException
     */
    public function execute()
    {
        $order_ids = $this->getRequest()->getQuery('order_ids');
        $orders = $this->orderCollectionFactory
            ->create()
            ->addFieldToFilter('entity_id', ['in' => $order_ids])
            ->load();

        $packageIds = [];
        foreach ($orders as $order) {
            $tracksCollection = $order->getTracksCollection();
            foreach ($tracksCollection->getItems() as $track) {
                $packageIds[] = $track->getTrackNumber();
            }
        }

        if (empty($packageIds)) {
            $result = $this->rawFactory->create();
            $result->setContents(
                __('No shipments found in any of the orders!') . '<br>' .
                __('Create a shipping label for each order!') . '<br>' .
                '<button onclick="history.back()">' . __('Go Back') . '</button>'
            );
            return $result;
        }

        $request = [
            'secret' => $this->backendConfig->getValue(self::SECRET_KEY_PATH),
            'parcels' => $packageIds
        ];

        $response = $this->doRequest($request);

        $responseContentType = $response->getHeader('Content-Type')[0] ?? '';
        if ($responseContentType !== 'application/pdf') {
            $responseJson = json_decode($response->getBody());
            if (!empty($responseJson->error)) {
                throw new LocalizedException(
                    new Phrase($responseJson->error)
                );
            }
            throw new LocalizedException(
                new Phrase(__('Could not get the manifest!'))
            );
        }

        $date = date('Y-m-d H:i:s');
        foreach ($orders as $order) {
            $order->setManifestDate($date);
            $this->orderResourceModel->save($order);
        }

        $fileName = 'Express_manifest_' . date('Y_m_d_H_i') . '.pdf';
        $manifestFileContent = [
            'type' => 'string',
            'value' => $response->getBody()->getContents(),
        ];

        try {
            return $this->fileFactory->create($fileName, $manifestFileContent, 'base', 'application/pdf');
        } catch (\Exception $e) {
            throw new LocalizedException(
                new Phrase(__('Could not open the manifest'))
            );
        }
    }

    public function doRequest($params)
    {
        $client = $this->clientFactory->create(['config' => [
            'base_uri' => self::REQUEST_URI
        ]]);

        try {
            $response = $client->request(
                Request::HTTP_METHOD_POST,
                self::REQUEST_ENDPOINT,
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
