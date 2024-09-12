<?php

namespace WebDev\LatvijasPasts\Api;

interface CountryManagementInterface
{
    /**
     * Get Valid Countries of ExpressPasts Shipping
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
