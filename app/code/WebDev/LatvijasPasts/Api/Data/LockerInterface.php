<?php

namespace WebDev\LatvijasPasts\Api\Data;

interface LockerInterface
{
    /**
     * Get Id
     * @return string
     */
    public function getId();

    /**
     * Get Zip
     * @return string
     */
    public function getZip();

    /**
     * Get Code
     * @return string
     */
    public function getCode();

    /**
     * Get Title
     * @return string
     */
    public function getTitle();

    /**
     * Get Additional Info
     * @return string
     */
    public function getAdditionalInfo();

    /**
     * Get Address
     * @return string
     */
    public function getAddress();
}
