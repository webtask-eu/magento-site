<?php

namespace WebDev\LatvijasPasts\Plugin\Sales\Model\AdminOrder;

use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\AdminOrder\Create;
use WebDev\LatvijasPasts\Api\LockerLithuaniaManagementInterface;
use WebDev\LatvijasPasts\Api\PostOfficeLithuaniaManagementInterface;
use WebDev\LatvijasPasts\Model\Carrier;
use WebDev\LatvijasPasts\Api\LockerManagementInterface;
use WebDev\LatvijasPasts\Api\PostOfficeManagementInterface;
use WebDev\LatvijasPasts\Api\CircleKDusManagementInterface;

class CreatePlugin
{
    private RequestInterface $request;
    private Carrier $carrier;
    private CircleKDusManagementInterface $circleKDusManagement;
    private LockerManagementInterface $lockerManagement;
    private LockerLithuaniaManagementInterface $lockerLithuaniaManagement;
    private PostOfficeManagementInterface $postOfficeManagement;
    private PostOfficeLithuaniaManagementInterface $postOfficeLithuaniaManagement;

    public function __construct(
        RequestInterface                       $request,
        Carrier                                $carrier,
        CircleKDusManagementInterface          $circleKDusManagement,
        LockerManagementInterface              $lockerManagement,
        LockerLithuaniaManagementInterface     $lockerLithuaniaManagement,
        PostOfficeManagementInterface          $postOfficeManagement,
        PostOfficeLithuaniaManagementInterface $postOfficeLithuaniaManagement,
    )
    {
        $this->request = $request;
        $this->carrier = $carrier;
        $this->circleKDusManagement = $circleKDusManagement;
        $this->lockerManagement = $lockerManagement;
        $this->lockerLithuaniaManagement = $lockerLithuaniaManagement;
        $this->postOfficeManagement = $postOfficeManagement;
        $this->postOfficeLithuaniaManagement = $postOfficeLithuaniaManagement;
    }

    public function afterSetShippingAddress(Create $subject, $result)
    {
        $shippingAddress = $result->getQuote()->getShippingAddress();
        $params = $this->request->getParams();

        if (isset($params['order']['latvijas_pasts_pickup_point'])) {
            $shippingMethod = $shippingAddress->getData('shipping_method');
            $pickupPointId = $params['order']['latvijas_pasts_pickup_point'];

            $pickupPoints = match ($shippingMethod) {
                $this->carrier->getCircleKDusMethodCodeFull() => $this->getCircleKDus(),
                $this->carrier->getLockerMethodCodeFull() => $this->getLockers(),
                $this->carrier->getLockerLithuaniaMethodCodeFull() => $this->getLockersLithuania(),
                $this->carrier->getPostOfficeMethodCodeFull() => $this->getPostOffices(),
                $this->carrier->getPostOfficeLithuaniaMethodCodeFull() => $this->getPostOfficesLithuania(),
                default => [],
            };

            $pickupPointJson = null;
            foreach ($pickupPoints as $pickupPoint) {
                if ($pickupPoint['id'] == $pickupPointId) {
                    $pickupPointJson = json_encode($pickupPoint);
                    break;
                }
            }

            if ($pickupPointJson) {
                $pickupPointData = json_decode($pickupPointJson, true);

                $shippingAddress->setData('save_in_address_book', '0');
                $shippingAddress->setData('postcode', $pickupPointData['zipcode']);
                $shippingAddress->setData('region', $pickupPointData['magento_region_name']);
                $shippingAddress->setData('region_id', $pickupPointData['magento_region_id']);
                $shippingAddress->setData('region_code', $pickupPointData['magento_region_code']);
                $street = $pickupPointData['street'] . ' ' . $pickupPointData['house'];
                if (isset($pickupPointData['region'])) {
                    $street = $street . ', ' . $pickupPointData['region'];
                }
                $shippingAddress->setData('street', [$street]);
                $shippingAddress->setData('city', $pickupPointData['city'] ?? $pickupPointData['village'] ?? '');
                $shippingAddress->setData('latvijas_pasts_json', $pickupPointJson);

                $result->getQuote()->setShippingAddress($shippingAddress);
            }
        }
        return $result;
    }

    public function getCircleKDus()
    {
        return $this->circleKDusManagement->fetch();
    }

    public function getLockers()
    {
        return $this->lockerManagement->fetch();
    }

    public function getLockersLithuania()
    {
        return $this->lockerLithuaniaManagement->fetch();
    }

    public function getPostOffices()
    {
        return $this->postOfficeManagement->fetch();
    }

    public function getPostOfficesLithuania()
    {
        return $this->postOfficeLithuaniaManagement->fetch();
    }
}
