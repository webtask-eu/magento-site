<?php

namespace WebDev\LatvijasPasts\Model\Resource;

use Magento\Checkout\Model\ConfigProviderInterface;
use WebDev\LatvijasPasts\Api\LockerManagementInterface;

class LockerConfigProvider implements ConfigProviderInterface
{
    private LockerManagementInterface $lockerManagement;

    public function __construct(
        LockerManagementInterface $lockerManagement
    )
    {
        $this->lockerManagement = $lockerManagement;
    }

    public function getConfig()
    {
        $config = [];
        $config['locker_data'] = $this->lockerManagement->fetch();
        return $config;
    }
}
