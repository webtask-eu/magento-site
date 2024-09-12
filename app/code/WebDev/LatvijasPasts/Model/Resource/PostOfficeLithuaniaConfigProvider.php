<?php

namespace WebDev\LatvijasPasts\Model\Resource;

use Magento\Checkout\Model\ConfigProviderInterface;
use WebDev\LatvijasPasts\Api\PostOfficeLithuaniaManagementInterface;

class PostOfficeLithuaniaConfigProvider implements ConfigProviderInterface
{
    private PostOfficeLithuaniaManagementInterface $postLithuaniaOfficeManagement;

    public function __construct(
        PostOfficeLithuaniaManagementInterface $postLithuaniaOfficeManagement
    )
    {
        $this->postLithuaniaOfficeManagement = $postLithuaniaOfficeManagement;
    }

    public function getConfig()
    {
        $config = [];
        $config['post_office_lithuania_data'] = $this->postLithuaniaOfficeManagement->fetch();
        return $config;
    }
}
