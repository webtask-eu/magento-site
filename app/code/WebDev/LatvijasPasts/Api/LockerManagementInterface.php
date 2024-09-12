<?php

namespace WebDev\LatvijasPasts\Api;

interface LockerManagementInterface
{
    /**
     * Find lockers for the customer
     *
     * @return array
     */
    public function fetch();

    /**
     * Get Local File
     *
     * @return string
     */
    public function getLocalFile();

    /**
     * Get Api Endpoint
     *
     * @return string
     */
    public function getApiEndpoint();
}
