<?php

namespace WebDev\LatvijasPasts\Api;

interface LockerLithuaniaManagementInterface
{
    /**
     * Get Lithuania Lockers List
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
