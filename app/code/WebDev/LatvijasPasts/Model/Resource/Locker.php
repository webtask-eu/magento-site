<?php

namespace WebDev\LatvijasPasts\Model\Resource;

use Magento\Framework\DataObject;
use WebDev\LatvijasPasts\Api\Data\LockerInterface;

class Locker extends DataObject implements LockerInterface
{
    public function getId()
    {
        return (string)$this->_getData('id');
    }

    public function getZip()
    {
        return (string)$this->_getData('zipcode');
    }

    public function getCode()
    {
        return (string)$this->_getData('code');
    }

    public function getTitle()
    {
        return (string)$this->_getData('title');
    }

    public function getAdditionalInfo()
    {
        return (string)$this->_getData('additional_information');
    }

    public function getAddress()
    {
        return (string)$this->_getData('address');
    }
}
