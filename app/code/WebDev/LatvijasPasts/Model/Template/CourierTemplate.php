<?php

namespace WebDev\LatvijasPasts\Model\Template;

use Magento\Directory\Model\ResourceModel\Region\Collection as RegionCollection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class CourierTemplate extends Template
{
    private ScopeConfigInterface $scopeConfig;
    private RegionCollection $regionCollection;

    public function __construct(
        Context              $context,
        ScopeConfigInterface $scopeConfig,
        RegionCollection     $regionCollection,
    )
    {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->regionCollection = $regionCollection;
    }

    public function getStoreName()
    {
        return $this->scopeConfig->getValue('general/store_information/name') ?: '';
    }

    public function getStorePhone()
    {
        $phone = $this->scopeConfig->getValue('general/store_information/phone');
        if (!$phone) {
            return '';
        }
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        return $phone ?: '';
    }

    public function getCountryCode()
    {
        return $this->scopeConfig->getValue('general/store_information/country_id') ?: '';
    }

    public function getRegion()
    {
        $regionId = $this->scopeConfig->getValue('general/store_information/region_id');
        if (!$regionId) {
            return '';
        }
        return $this->regionCollection
            ->addFieldToFilter('main_table.region_id', ['eq' => $regionId])
            ->getFirstItem()
            ->getDefaultName();
    }

    public function getCity()
    {
        return $this->scopeConfig->getValue('general/store_information/city') ?: '';
    }

    public function getStreet()
    {
        $address = $this->scopeConfig->getValue('general/store_information/street_line1');
        return $address
            ? trim(preg_replace("/[^\s\d]*\d\S*/", "", $address)) //remove words with numbers
            : '';
    }

    public function getHouse()
    {
        $address = $this->scopeConfig->getValue('general/store_information/street_line1');
        if (!$address) {
            return '';
        }
        $address = explode(' ', $address);
        if (count($address) <= 1) {
            return '';
        }
        return end($address);
    }

    public function getZipcode()
    {
        $zipcode = $this->scopeConfig->getValue('general/store_information/postcode');
        if (!$zipcode) {
            return '';
        }
        $zipcode = preg_replace('/[^0-9]/', '', $zipcode);
        $countryCode = $this->getCountryCode();
        if (!$countryCode) {
            return $zipcode;
        }
        return $countryCode . '-' . $zipcode;
    }

    public function getComments()
    {
        return $this->scopeConfig->getValue('general/store_information/street_line2') ?: '';
    }
}
