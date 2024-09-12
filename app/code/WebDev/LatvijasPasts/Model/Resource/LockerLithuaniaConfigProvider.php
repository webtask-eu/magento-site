<?php

namespace WebDev\LatvijasPasts\Model\Resource;

use Magento\Checkout\Model\ConfigProviderInterface;
use WebDev\LatvijasPasts\Api\LockerLithuaniaManagementInterface;

class LockerLithuaniaConfigProvider implements ConfigProviderInterface
{
    private LockerLithuaniaManagementInterface $lockerLithuaniaManagement;

    public function __construct(
        LockerLithuaniaManagementInterface $lockerLithuaniaManagement
    )
    {
        $this->lockerLithuaniaManagement = $lockerLithuaniaManagement;
    }

    public function getConfig()
    {
        $config = [];
        $config['locker_lithuania_data'] = $this->lockerLithuaniaManagement->fetch();
        return $config;
    }
}
