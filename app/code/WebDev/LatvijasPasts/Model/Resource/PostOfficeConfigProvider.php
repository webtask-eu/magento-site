<?php

namespace WebDev\LatvijasPasts\Model\Resource;

use Magento\Checkout\Model\ConfigProviderInterface;
use WebDev\LatvijasPasts\Api\PostOfficeManagementInterface;

class PostOfficeConfigProvider implements ConfigProviderInterface
{
    private PostOfficeManagementInterface $postOfficeManagement;

    public function __construct(
        PostOfficeManagementInterface $postOfficeManagement
    )
    {
        $this->postOfficeManagement = $postOfficeManagement;
    }

    public function getConfig()
    {
        $config = [];
        $config['post_office_data'] = $this->postOfficeManagement->fetch();
        return $config;
    }
}
