<?php

namespace WebDev\LatvijasPasts\Block\Adminhtml\Order\Packaging;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Model\OrderRepository;
use WebDev\LatvijasPasts\Model\Carrier;

class Select extends Template
{
    protected $_template = 'order/packaging/select.phtml';
    private Carrier $carrier;
    private OrderRepository $orderRepository;
    private ShipmentRepositoryInterface $shipmentRepository;

    public function __construct(
        Context                     $context,
        Carrier                     $carrier,
        OrderRepository             $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        array                       $data = []
    )
    {
        parent::__construct($context, $data);
        $this->carrier = $carrier;
        $this->orderRepository = $orderRepository;
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * @throws NoSuchEntityException
     * @throws InputException
     */
    public function getOrderShippingethod()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if (!$orderId) {
            $shipmentId = $this->getRequest()->getParam('shipment_id');
            if (!$shipmentId) {
                return false;
            }
            $shipment = $this->shipmentRepository->get($shipmentId);
            $orderId = $shipment->getOrderId();
        }
        $order = $this->orderRepository->get($orderId);
        $shippingMethod = $order->getShippingMethod();
        if (!$shippingMethod) {
            return false;
        }
        return $shippingMethod;
    }

    public function isLatvijasPastsMethod()
    {
        $shippingMethod = $this->getOrderShippingethod();
        if (!$shippingMethod) {
            return false;
        }
        return in_array($shippingMethod, [
            $this->carrier->getMansPastsMethodCodeFull(),
            $this->carrier->getExpressMethodCodeFull(),
            $this->carrier->getPostOfficeMethodCodeFull(),
            $this->carrier->getLockerMethodCodeFull(),
            $this->carrier->getPostOfficeLithuaniaMethodCodeFull(),
            $this->carrier->getLockerLithuaniaMethodCodeFull(),
            $this->carrier->getCircleKDusMethodCodeFull(),
        ]);
    }

    public function isMansPastsMethod()
    {
        return $this->getOrderShippingethod() === $this->carrier->getMansPastsMethodCodeFull();
    }

    public function isLockerLithuaniaMethod()
    {
        return $this->getOrderShippingethod() === $this->carrier->getLockerLithuaniaMethodCodeFull();
    }

    public function isExpressPastsMethod()
    {
        $shippingMethod = $this->getOrderShippingethod();
        if (!$shippingMethod) {
            return false;
        }
        return in_array($shippingMethod, [
            $this->carrier->getExpressMethodCodeFull(),
            $this->carrier->getPostOfficeMethodCodeFull(),
            $this->carrier->getLockerMethodCodeFull(),
            $this->carrier->getPostOfficeLithuaniaMethodCodeFull(),
            $this->carrier->getLockerLithuaniaMethodCodeFull(),
            $this->carrier->getCircleKDusMethodCodeFull(),
        ]);
    }
}
