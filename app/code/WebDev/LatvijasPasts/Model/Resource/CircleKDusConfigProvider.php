<?php

namespace WebDev\LatvijasPasts\Model\Resource;

use Magento\Checkout\Model\ConfigProviderInterface;
use WebDev\LatvijasPasts\Api\CircleKDusManagementInterface;

class CircleKDusConfigProvider implements ConfigProviderInterface
{
    private CircleKDusManagementInterface $circleKDusManagement;

    public function __construct(
        CircleKDusManagementInterface $circleKDusManagement
    )
    {
        $this->circleKDusManagement = $circleKDusManagement;
    }

    public function getConfig()
    {
        $config = [];
        $config['circle_k_dus_data'] = $this->circleKDusManagement->fetch();
        return $config;
    }
}
