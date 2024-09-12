<?php

namespace WebDev\LatvijasPasts\Model\Resource;

use Magento\Framework\DataObject;
use WebDev\LatvijasPasts\Api\Data\CountryInterface;

class Country extends DataObject implements CountryInterface
{
    public function getId()
    {
        return (string)$this->_getData('id');
    }

    public function getIso2()
    {
        return (string)$this->_getData('iso2');
    }

    public function getIso3()
    {
        return (string)$this->_getData('iso3');
    }

    public function getTitleLv()
    {
        return (string)$this->_getData('title_lv');
    }

    public function getTitleEn()
    {
        return (string)$this->_getData('title_en');
    }

    public function getTitleRu()
    {
        return (string)$this->_getData('title_ru');
    }

    public function getMaxWeight()
    {
        return (string)$this->_getData('max_weight');
    }

    public function getEmsCountry()
    {
        return (string)$this->_getData('ems_country');
    }

    public function getEpg()
    {
        return (string)$this->_getData('epg');
    }

    public function getHsCode()
    {
        return (string)$this->_getData('hs_code');
    }
}
