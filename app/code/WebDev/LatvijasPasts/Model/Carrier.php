<?php

namespace WebDev\LatvijasPasts\Model;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Shipping\Model\Simplexml\ElementFactory;
use Magento\Shipping\Model\Tracking\ResultFactory as TrackResultFactory;
use Magento\Shipping\Model\Tracking\Result\ErrorFactory as TrackErrorFactory;
use Magento\Shipping\Model\Tracking\Result\StatusFactory as TrackStatusFactory;
use Magento\Directory\Model\RegionFactory;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Directory\Helper\Data;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Xml\Security;
use Magento\Framework\DataObject;
use Psr\Log\LoggerInterface;

class Carrier extends AbstractCarrierOnline implements CarrierInterface
{
    protected $_code = 'latvijaspasts';

    private const MANS_PASTS_COURIER_CODE = 'MANS_PASTS_COURIER';
    private const MANS_PASTS_COURIER_LABEL = 'MansPasts Courier';

    private const EXPRESS_COURIER_CODE = 'EXPRESS_COURIER';
    private const EXPRESS_COURIER_LABEL = 'ExpressPasts Courier';

    private const POST_OFFICE_CODE = 'POST_OFFICE';
    private const POST_OFFICE_LABEL = 'Latvia Post Office';
    private const POST_OFFICE_LT_CODE = 'POST_OFFICE_LT';
    private const POST_OFFICE_LT_LABEL = 'Lithuania Post Office';

    private const LOCKER_CODE = 'LOCKER';
    private const LOCKER_LABEL = 'Latvia Post Terminal';
    private const LOCKER_LT_CODE = 'LOCKER_LT';
    private const LOCKER_LT_LABEL = 'Lithuania Post Terminal';

    private const CIRCLE_K_DUS_CODE = 'CIRCLE_K_DUS';
    private const CIRCLE_K_DUS_LABEL = 'CircleK DUS';

    private ExpressPastsParcelCreate $expressPastsParcelCreate;
    private MansPastsParcelCreate $mansPastsParcelCreate;
    private ExpressPastsShippingLabel $expressPastsShippingLabel;
    private MansPastsShippingLabel $mansPastsShippingLabel;
    private Session $_checkoutSession;
    private RequestInterface $httpRequest;

    public function __construct(
        ScopeConfigInterface      $scopeConfig,
        ErrorFactory              $rateErrorFactory,
        LoggerInterface           $logger,
        Security                  $xmlSecurity,
        ElementFactory            $xmlElFactory,
        ResultFactory             $rateResultFactory,
        MethodFactory             $rateMethodFactory,
        TrackResultFactory        $trackFactory,
        TrackErrorFactory         $trackErrorFactory,
        TrackStatusFactory        $trackStatusFactory,
        RegionFactory             $regionFactory,
        CountryFactory            $countryFactory,
        CurrencyFactory           $currencyFactory,
        Data                      $directoryData,
        StockRegistryInterface    $stockRegistry,
        ExpressPastsParcelCreate  $expressPastsParcelCreate,
        MansPastsParcelCreate     $mansPastsParcelCreate,
        ExpressPastsShippingLabel $expressPastsShippingLabel,
        MansPastsShippingLabel    $mansPastsShippingLabel,
        Session                   $checkoutSession,
        RequestInterface          $httpRequest,
        array                     $data = []
    )
    {
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $xmlSecurity,
            $xmlElFactory,
            $rateResultFactory,
            $rateMethodFactory,
            $trackFactory,
            $trackErrorFactory,
            $trackStatusFactory,
            $regionFactory,
            $countryFactory,
            $currencyFactory,
            $directoryData,
            $stockRegistry,
            $data
        );
        $this->expressPastsParcelCreate = $expressPastsParcelCreate;
        $this->mansPastsParcelCreate = $mansPastsParcelCreate;
        $this->expressPastsShippingLabel = $expressPastsShippingLabel;
        $this->mansPastsShippingLabel = $mansPastsShippingLabel;
        $this->_checkoutSession = $checkoutSession;
        $this->httpRequest = $httpRequest;
    }

    public function getAllowedMethods()
    {
        $allowed = explode(',', $this->getConfigData('allowed_methods'));
        $arr = [];
        foreach ($allowed as $k) {
            $arr[$k] = $this->getCode('method', $k);
        }

        return $arr;
    }

    /**
     * @throws LocalizedException
     */
    protected function _doShipmentRequest(DataObject $request)
    {
        $packages = $request->getOrderShipment()->getPackages();
        $packageCount = count($packages);
        if ($packageCount !== 1) {
            throw new LocalizedException(
                new Phrase(__('LatvijasPasts shipment cannot contain more than 1 package! Found %1 packages!'), [$packageCount])
            );
        }
        $package = $packages[array_key_first($packages)];
        $weightUnit = $package['params']['weight_units'];
        if ($weightUnit !== 'KILOGRAM') {
            throw new LocalizedException(
                new Phrase(__('Unsupported package weight unit: %1! Please select kilogram.'), [$weightUnit])
            );
        }
        $order = $request->getOrderShipment()->getOrder();
        $method = $request->getShippingMethod();
        $expressPastsMethods = [
            self::EXPRESS_COURIER_CODE,
            self::POST_OFFICE_CODE,
            self::POST_OFFICE_LT_CODE,
            self::LOCKER_CODE,
            self::LOCKER_LT_CODE,
            self::CIRCLE_K_DUS_CODE
        ];
        if ($method === self::MANS_PASTS_COURIER_CODE) {
            $packageType = $this->httpRequest->getParam('manspasts_package_type');
            $trackingNumber = $this->mansPastsParcelCreate->request($order, $package, $packageType);
        } elseif ($method === self::LOCKER_LT_CODE) {
            $packagePriority = $this->httpRequest->getParam('expresspasts_package_package_priority');
            $packageSize = $this->httpRequest->getParam('expresspasts_package_package_size');
            $trackingNumber = $this->expressPastsParcelCreate->request($order, $package, $packagePriority, $packageSize);
        } elseif (in_array($method, $expressPastsMethods)) {
            $packagePriority = $this->httpRequest->getParam('expresspasts_package_package_priority');
            $trackingNumber = $this->expressPastsParcelCreate->request($order, $package, $packagePriority);
        }

        $result = new DataObject();
        if (empty($trackingNumber)) {
            throw new LocalizedException(
                new Phrase(__('Failed to create shipment!'))
            );
        }

        if ($method === self::MANS_PASTS_COURIER_CODE) {
            $shippingLabel = $this->mansPastsShippingLabel->requestLabel($order, $trackingNumber);
        } else {
            $shippingLabel = $this->expressPastsShippingLabel->requestLabel($order, $trackingNumber);
        }

        if (!$shippingLabel) {
            throw new LocalizedException(
                new Phrase(__('Failed to create shipping label!'))
            );
        }
        $result->setTrackingNumber([$trackingNumber]);
        $result->setShippingLabelContent($shippingLabel);
        return $result;
    }

    public function collectRates(RateRequest $request)
    {
        if (!$this->canCollectRates()) {
            return $this->getErrorMessage();
        }
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        $result = $this->_rateFactory->create();
        $allowedMethods = explode(',', $this->getConfigData('allowed_methods'));

        foreach ($allowedMethods as $allowedMethod) {
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier('latvijaspasts');
            $method->setCarrierTitle($this->getConfigData('title'));
            $method->setMethod($allowedMethod);
            $method->setMethodTitle($this->getCode('method', $allowedMethod));

            $countryCode = $this->_checkoutSession->getQuote()
                ->getShippingAddress()
                ->getCountryId();

            $amount = match ($allowedMethod) {
                self::CIRCLE_K_DUS_CODE => $countryCode === 'LV'
                    ? $this->getConfigData('price_circle_k_dus')
                    : null,
                self::LOCKER_CODE => $countryCode === 'LV'
                    ? $this->getConfigData('price_post_locker')
                    : null,
                self::POST_OFFICE_CODE => $countryCode === 'LV'
                    ? $this->getConfigData('price_post_office')
                    : null,
                self::LOCKER_LT_CODE => $countryCode === 'LT'
                    ? $this->getConfigData('price_post_locker')
                    : null,
                self::POST_OFFICE_LT_CODE => $countryCode === 'LT'
                    ? $this->getConfigData('price_post_office')
                    : null,
                self::EXPRESS_COURIER_CODE => match ($countryCode) {
                    'LV' => $this->getConfigData('price_express_courier_lv'),
                    'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU',
                    'IE', 'IT', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE'
                    => $this->getConfigData('price_express_courier_eu'),
                    default => $this->getConfigData('price_express_courier'),
                },
                self::MANS_PASTS_COURIER_CODE => match ($countryCode) {
                    'LV' => $this->getConfigData('price_manspasts_courier_lv'),
                    'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU',
                    'IE', 'IT', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE'
                    => $this->getConfigData('price_manspasts_courier_eu'),
                    default => $this->getConfigData('price_manspasts_courier'),
                },
                default => null,
            };
            if (!isset($amount)) {
                continue;
            }

            $amount = $this->getFinalPriceWithHandlingFee($amount);
            $method->setPrice($amount);
            $method->setCost($amount);

            $result->append($method);
        }

        return $result;
    }

    public function isTrackingAvailable()
    {
        return true;
    }

    public function getTrackingInfo($tracking)
    {
        $result = $this->_trackFactory->create();

        $trackingResult = $this->_trackStatusFactory->create();
        $trackingResult->setCarrier($this->_code);
        $trackingResult->setCarrierTitle($this->getConfigData('title'));
        $trackingResult->setTracking($tracking);
        $trackingResult->setUrl(
            'https://track.pasts.lv/consignment/tracking?type=pasts&button=find&id=' . $tracking
        );

        $result->append($trackingResult);
        return $trackingResult;
    }

    public function getCode($type, $code = '')
    {
        $codes = [
            'method' => [
                self::EXPRESS_COURIER_CODE => __('%1', self::EXPRESS_COURIER_LABEL),
                self::MANS_PASTS_COURIER_CODE => __('%1', self::MANS_PASTS_COURIER_LABEL),
                self::POST_OFFICE_CODE => __('%1', self::POST_OFFICE_LABEL),
                self::LOCKER_CODE => __('%1', self::LOCKER_LABEL),
                self::POST_OFFICE_LT_CODE => __('%1', self::POST_OFFICE_LT_LABEL),
                self::LOCKER_LT_CODE => __('%1', self::LOCKER_LT_LABEL),
                self::CIRCLE_K_DUS_CODE => __('%1', self::CIRCLE_K_DUS_LABEL),
            ],
        ];

        if (!isset($codes[$type])) {
            return false;
        } elseif ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            return false;
        } else {
            return $codes[$type][$code];
        }
    }

    public function getAllAvailableMethodsForPickup()
    {
        return [
            self::LOCKER_CODE,
            self::LOCKER_LT_CODE,
            self::POST_OFFICE_CODE,
            self::POST_OFFICE_LT_CODE,
            self::CIRCLE_K_DUS_CODE
        ];
    }

    public function getAllAvailableMethods()
    {
        return [
            self::LOCKER_CODE,
            self::LOCKER_LT_CODE,
            self::POST_OFFICE_CODE,
            self::POST_OFFICE_LT_CODE,
            self::CIRCLE_K_DUS_CODE,
            self::EXPRESS_COURIER_CODE,
            self::MANS_PASTS_COURIER_CODE
        ];
    }

    public function getAllAvailableMethodsForPickupWithCode()
    {
        $codes = [];
        foreach ($this->getAllAvailableMethodsForPickup() as $code) {
            $codes[] = $this->getCarrierCode() . '_' . $code;
        }

        return $codes;
    }

    public function getAllAvailableMethodsWithCode()
    {
        $codes = [];
        foreach ($this->getAllAvailableMethods() as $code) {
            $codes[] = $this->getCarrierCode() . '_' . $code;
        }

        return $codes;
    }

    public function getMansPastsMethodCode()
    {
        return self::MANS_PASTS_COURIER_CODE;
    }

    public function getMansPastsMethodCodeFull()
    {
        return $this->getCarrierCode() . '_' . self::MANS_PASTS_COURIER_CODE;
    }

    public function getMansPastsMethodLabel()
    {
        return __('%1', self::MANS_PASTS_COURIER_LABEL);
    }

    public function getExpressMethodCode()
    {
        return self::EXPRESS_COURIER_CODE;
    }

    public function getExpressMethodCodeFull()
    {
        return $this->getCarrierCode() . '_' . self::EXPRESS_COURIER_CODE;
    }

    public function getExpressMethodLabel()
    {
        return __('%1', self::EXPRESS_COURIER_LABEL);
    }

    public function getPostOfficeMethodCode()
    {
        return self::POST_OFFICE_CODE;
    }

    public function getPostOfficeMethodCodeFull()
    {
        return $this->getCarrierCode() . '_' . self::POST_OFFICE_CODE;
    }

    public function getPostOfficeMethodLabel()
    {
        return __('%1', self::POST_OFFICE_LABEL);
    }

    public function getLockerMethodCode()
    {
        return self::LOCKER_CODE;
    }

    public function getLockerMethodCodeFull()
    {
        return $this->getCarrierCode() . '_' . self::LOCKER_CODE;
    }

    public function getLockerMethodLabel()
    {
        return __('%1', self::LOCKER_LABEL);
    }

    public function getPostOfficeLithuaniaMethodCode()
    {
        return self::POST_OFFICE_LT_CODE;
    }

    public function getPostOfficeLithuaniaMethodCodeFull()
    {
        return $this->getCarrierCode() . '_' . self::POST_OFFICE_LT_CODE;
    }

    public function getPostOfficeLithuaniaMethodLabel()
    {
        return __('%1', self::POST_OFFICE_LT_LABEL);
    }

    public function getLockerLithuaniaMethodCode()
    {
        return self::LOCKER_LT_CODE;
    }

    public function getLockerLithuaniaMethodCodeFull()
    {
        return $this->getCarrierCode() . '_' . self::LOCKER_LT_CODE;
    }

    public function getLockerLithuaniaMethodLabel()
    {
        return __('%1', self::LOCKER_LT_LABEL);
    }

    public function getCircleKDusMethodCode()
    {
        return self::CIRCLE_K_DUS_CODE;
    }

    public function getCircleKDusMethodCodeFull()
    {
        return $this->getCarrierCode() . '_' . self::CIRCLE_K_DUS_CODE;
    }

    public function getCircleKDusMethodLabel()
    {
        return __('%1', self::CIRCLE_K_DUS_LABEL);
    }
}
