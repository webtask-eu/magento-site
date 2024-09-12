<?php

namespace WebDev\LatvijasPasts\Model\Resource;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File as filesystemDriver;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader;
use WebDev\LatvijasPasts\Api\Data\LockerInterface;
use WebDev\LatvijasPasts\Api\LockerManagementInterface;

class LockerManagement implements LockerManagementInterface
{
    private Reader $reader;
    private FilesystemDriver $filesystemDriver;
    private const SOURCE_FILE_NAME = 'locker.json';
    private const API_ENDPOINT = 'https://express.pasts.lv/stationApi/index';

    public function __construct(
        Reader           $reader,
        FilesystemDriver $filesystemDriver
    )
    {
        $this->reader = $reader;
        $this->filesystemDriver = $filesystemDriver;
    }

    /**
     * @throws FileSystemException
     */
    public function fetch()
    {
        $locationFile = $this->getLocalFile();
        if (!$this->filesystemDriver->isExists($locationFile)) {
            return [];
        }

        return json_decode($this->filesystemDriver->fileGetContents($locationFile), true);
    }

    public function getLocalFile()
    {
        return $this->reader->getModuleDir(Dir::MODULE_ETC_DIR, 'WebDev_LatvijasPasts')
            . DIRECTORY_SEPARATOR . self::SOURCE_FILE_NAME;
    }

    public function getApiEndpoint()
    {
        return self::API_ENDPOINT;
    }
}
