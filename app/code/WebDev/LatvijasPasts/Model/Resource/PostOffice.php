<?php

namespace WebDev\LatvijasPasts\Model\Resource;

use Magento\Framework\DataObject;
use WebDev\LatvijasPasts\Api\Data\PostOfficeInterface;

class PostOffice extends DataObject implements PostOfficeInterface
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
        return (string)$this->_getData('postcode');
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
