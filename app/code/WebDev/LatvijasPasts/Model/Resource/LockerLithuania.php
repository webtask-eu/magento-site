<?php

namespace WebDev\LatvijasPasts\Model\Resource;

use Magento\Framework\DataObject;
use WebDev\LatvijasPasts\Api\Data\LockerLithuaniaInterface;

class LockerLithuania extends DataObject implements LockerLithuaniaInterface
{
    public function getId()
    {
        return (string)$this->_getData('id');
    }

    public function getZip()
    {
        return (string)$this->_getData('postal_code');
    }

    public function getCode()
    {
        return (string)$this->_getData('code');
    }

    public function getTitle()
    {
        return (string)$this->_getData('title');
    }

    public function getAddress()
    {
        return (string)$this->_getData('address');
    }
}
