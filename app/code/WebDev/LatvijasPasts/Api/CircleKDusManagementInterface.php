<?php

namespace WebDev\LatvijasPasts\Api;

interface CircleKDusManagementInterface
{
    /**
     * Get CircleK Locations
     * @return array
     */
    public function fetch();

    /**
     * Get Local File
     * @return string
     */
    public function getLocalFile();

    /**
     * Get Api Endpoint
     * @return string
     */
    public function getApiEndpoint();
}
