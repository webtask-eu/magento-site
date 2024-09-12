<?php

namespace WebDev\LatvijasPasts\Api\Data;

interface LockerLithuaniaInterface
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
     * Get Address
     * @return string
     */
    public function getAddress();
}
