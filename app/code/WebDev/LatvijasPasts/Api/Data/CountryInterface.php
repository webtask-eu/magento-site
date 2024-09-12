<?php

namespace WebDev\LatvijasPasts\Api\Data;

interface CountryInterface
{
    /**
     * Get Id
     * @return string
     */
    public function getId();

    /**
     * Get Iso2 Code
     * @return string
     */
    public function getIso2();

    /**
     * Get Iso3 Code
     * @return string
     */
    public function getIso3();

    /**
     * Get Title in Latvian
     * @return string
     */
    public function getTitleLv();

    /**
     * Get Title in English
     * @return string
     */
    public function getTitleEn();

    /**
     * Get Title in Russian
     * @return string
     */
    public function getTitleRu();

    /**
     * Get Max Package Weight
     * @return string
     */
    public function getMaxWeight();

    /**
     * Get Ems Country
     * @return string
     */
    public function getEmsCountry();

    /**
     * Get Epg
     * @return string
     */
    public function getEpg();

    /**
     * Get HS Code
     * @return string
     */
    public function getHsCode();
}
