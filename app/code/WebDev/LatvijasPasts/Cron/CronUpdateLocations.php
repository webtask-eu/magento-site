<?php

namespace WebDev\LatvijasPasts\Cron;

use Magento\Framework\Filesystem\Driver\File as FilesystemDriver;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use Magento\Framework\Phrase;
use Psr\Log\LoggerInterface;
use WebDev\LatvijasPasts\Api\CircleKDusManagementInterface;
use WebDev\LatvijasPasts\Api\CountryManagementInterface;
use WebDev\LatvijasPasts\Api\LockerLithuaniaManagementInterface;
use WebDev\LatvijasPasts\Api\LockerManagementInterface;
use WebDev\LatvijasPasts\Api\PostOfficeLithuaniaManagementInterface;
use WebDev\LatvijasPasts\Api\PostOfficeManagementInterface;
use WebDev\LatvijasPasts\Api\UsaStateManagementInterface;

class CronUpdateLocations
{
    private const LITHUANIA_REGIONS = [
        'Alytaus' => [
            'Alytaus', 'Alytus', 'Druskininkai', 'Lazdijai', 'Varėna', 'Veisiejai'
        ],
        'Kauno' => [
            'Kauno', 'Kaunas', 'Birštonas', 'Jonava', 'Kaišiadorys', 'Kėdainiai', 'Prienai', 'Raseiniai', 'Ariogala',
            'Babtai', 'Garliava', 'Karmėlava', 'Lapės', 'Mastaičiai', 'Narsiečiai', 'Noreikiškės', 'Pagiriai',
            'Raudondvaris', 'Rukla', 'Rumšiškės', 'Žiežmariai'
        ],
        'Klaipėdos' => [
            'Klaipėdos', 'Klaipėda', 'Kretinga', 'Neringa', 'Palanga', 'Skuodas', 'Šilutė', 'Gargždai', 'Giraitė',
            'Salantai', 'Švėkšna'
        ],
        'Marijampolės' => [
            'Marijampolės', 'Marijampolė', 'Kalvarija', 'Kazlų Rūda', 'Šakiai', 'Vilkaviškis', 'Kybartai', 'Pilviškiai'
        ],
        'Panevėžio' => [
            'Panevėžio', 'Panevėžys', 'Biržai', 'Kupiškis', 'Pasvalys', 'Rokiškis'
        ],
        'Šiaulių' => [
            'Šiaulių', 'Šiauliai', 'Akmenė', 'Joniškis', 'Kelmė', 'Pakruojis', 'Radviliškis', 'Baisogala', 'Ginkūnai',
            'Kuršėnai', 'Linkuva', 'Naujoji Akmenė', 'Šeduva', 'Tytuvėnai', 'Venta', 'Žagarė'
        ],
        'Tauragės' => [
            'Tauragės', 'Tauragė', 'Jurbarkas', 'Pagėgiai', 'Šilalė'
        ],
        'Telšių' => [
            'Telšių', 'Telšiai', 'Mažeikiai', 'Plungė', 'Rietavas', 'Viekšniai'
        ],
        'Utenos' => [
            'Utenos', 'Utena', 'Anykščiai', 'Ignalina', 'Molėtai', 'Visaginas', 'Zarasai'
        ],
        'Vilniaus' => [
            'Vilniaus', 'Vilnius', 'Elektrėnai', 'Šalčininkai', 'Širvintos', 'Švenčionys', 'Trakai', 'Ukmergė',
            'Antežeriai', 'Aukštadvaris', 'Eišiškės', 'Grigiškės', 'Jašiūnai', 'Lentvaris', 'Mickūnai', 'Nemenčinė',
            'Nemėžis', 'Pabradė', 'Riešė', 'Rudamina', 'Rūdiškės', 'Skaidiškės', 'Švenčionėliai', 'Vievis'
        ],
    ];

    private CircleKDusManagementInterface $circleKDusManagementInterface;
    private LockerManagementInterface $lockerManagementInterface;
    private LockerLithuaniaManagementInterface $lockerLithuaniaManagementInterface;
    private PostOfficeManagementInterface $postOfficeManagementInterface;
    private PostOfficeLithuaniaManagementInterface $postOfficeLithuaniaManagementInterface;
    private CountryManagementInterface $countryManagementInterface;
    private UsaStateManagementInterface $usaStateManagementInterface;
    private Curl $curl;
    private RegionCollectionFactory $regionCollectionFactory;
    private LoggerInterface $logger;
    private FilesystemDriver $filesystemDriver;

    public function __construct(
        CircleKDusManagementInterface          $circleKDusManagementInterface,
        LockerManagementInterface              $lockerManagementInterface,
        LockerLithuaniaManagementInterface     $lockerLithuaniaManagementInterface,
        PostOfficeManagementInterface          $postOfficeManagementInterface,
        PostOfficeLithuaniaManagementInterface $postOfficeLithuaniaManagementInterface,
        CountryManagementInterface             $countryManagementInterface,
        UsaStateManagementInterface            $usaStateManagementInterface,
        Curl                                   $curl,
        RegionCollectionFactory                $regionCollectionFactory,
        LoggerInterface                        $logger,
        FilesystemDriver                       $filesystemDriver
    )
    {
        $this->circleKDusManagementInterface = $circleKDusManagementInterface;
        $this->lockerManagementInterface = $lockerManagementInterface;
        $this->lockerLithuaniaManagementInterface = $lockerLithuaniaManagementInterface;
        $this->postOfficeManagementInterface = $postOfficeManagementInterface;
        $this->postOfficeLithuaniaManagementInterface = $postOfficeLithuaniaManagementInterface;
        $this->countryManagementInterface = $countryManagementInterface;
        $this->usaStateManagementInterface = $usaStateManagementInterface;
        $this->curl = $curl;
        $this->regionCollectionFactory = $regionCollectionFactory;
        $this->logger = $logger;
        $this->filesystemDriver = $filesystemDriver;
    }

    public function execute()
    {
        $this->updatePickupPoints();
        $this->updateExpressCountries();
    }

    /**
     * @throws FileSystemException
     * @throws LocalizedException
     */
    private function updatePickupPoints()
    {
        $circleLocalFile = $this->circleKDusManagementInterface->getLocalFile();
        $circleEndPoint = $this->circleKDusManagementInterface->getApiEndpoint();
        $this->updatePoint($circleLocalFile, $circleEndPoint);

        $postOfficeLocalFile = $this->postOfficeManagementInterface->getLocalFile();
        $postOfficeEndPoint = $this->postOfficeManagementInterface->getApiEndpoint();
        $this->updatePoint($postOfficeLocalFile, $postOfficeEndPoint);

        $postLithuaniaOfficeLocalFile = $this->postOfficeLithuaniaManagementInterface->getLocalFile();
        $postOfficeLithuaniaEndPoint = $this->postOfficeLithuaniaManagementInterface->getApiEndpoint();
        $this->updatePoint($postLithuaniaOfficeLocalFile, $postOfficeLithuaniaEndPoint);

        $lockerLocalFile = $this->lockerManagementInterface->getLocalFile();
        $lockerEndPoint = $this->lockerManagementInterface->getApiEndpoint();
        $this->updatePoint($lockerLocalFile, $lockerEndPoint);

        $lockerLithuaniaLocalFile = $this->lockerLithuaniaManagementInterface->getLocalFile();
        $lockerLithuaniaEndPoint = $this->lockerLithuaniaManagementInterface->getApiEndpoint();
        $this->updatePoint($lockerLithuaniaLocalFile, $lockerLithuaniaEndPoint);
    }

    /**
     * @throws FileSystemException
     * @throws LocalizedException
     */
    private function updateExpressCountries()
    {
        $countryLocalFile = $this->countryManagementInterface->getLocalFile();
        $countryEndPoint = $this->countryManagementInterface->getApiEndpoint();
        $this->updateCountry($countryLocalFile, $countryEndPoint);

        $usaStateLocalFile = $this->usaStateManagementInterface->getLocalFile();
        $usaStateEndPoint = $this->usaStateManagementInterface->getApiEndpoint();
        $this->updateCountry($usaStateLocalFile, $usaStateEndPoint);
    }

    /**
     * @throws FileSystemException
     * @throws LocalizedException
     */
    private function updatePoint(string $localFile, string $endPoint)
    {
        $this->curl->get($endPoint);

        if ($this->curl->getStatus() !== 200) {
            $message = new Phrase('Couldn\'t update the "%1" with the endpoint of "%2".', [$localFile, $endPoint]);
            $this->logger->critical($message);
            throw new LocalizedException($message);
        }

        $pickupPointsData = json_decode($this->curl->getBody(), true);
        $regionCollection = $this->regionCollectionFactory->create();
        $magentoRegions = [];

        $lithuaniaLocations = str_ends_with($localFile, 'lithuania.json');
        if ($lithuaniaLocations) {
            $regionCollection->addFieldToFilter('country_id', ['eq' => 'LT']);
            foreach ($regionCollection as $region) {
                $regName = str_replace(' Apskritis', '', $region->getName());

                $magentoRegions[$regName] = [
                    'region_id' => $region->getRegionId(),
                    'region_code' => $region->getCode(),
                    'region_name' => $region->getName(),
                ];
            }

            foreach ($pickupPointsData as $key => $pickupPointData) {
                if (!isset($pickupPointData['id'])) {
                    $pickupPointsData[$key] = ['id' => $pickupPointData['code']] + $pickupPointData;
                }
                $nameForSorting = $pickupPointData['city'] ?? $pickupPointData['area'] ?? $pickupPointData['village'] ?? '';
                $pickupPointsData[$key]['name_for_sorting'] = $nameForSorting;

                if (isset($pickupPointData['area']) && isset($magentoRegions[$pickupPointData['area']])) {
                    $magentoRegionId = $magentoRegions[$pickupPointData['area']]['region_id'];
                    $magentoRegionName = $magentoRegions[$pickupPointData['area']]['region_name'];
                    $magentoRegionCode = $magentoRegions[$pickupPointData['area']]['region_code'];

                    $pickupPointsData[$key]['magento_region_id'] = $magentoRegionId;
                    $pickupPointsData[$key]['magento_region_code'] = $magentoRegionCode;
                    $pickupPointsData[$key]['magento_region_name'] = $magentoRegionName;
                } else {
                    if ($foundRegion = $this->findLithuaniaRegion($nameForSorting)) {
                        $magentoRegionId = $magentoRegions[$foundRegion]['region_id'];
                        $magentoRegionName = $magentoRegions[$foundRegion]['region_name'];
                        $magentoRegionCode = $magentoRegions[$foundRegion]['region_code'];

                        $pickupPointsData[$key]['magento_region_id'] = $magentoRegionId;
                        $pickupPointsData[$key]['magento_region_code'] = $magentoRegionCode;
                        $pickupPointsData[$key]['magento_region_name'] = $magentoRegionName;
                    }
                }
            }

        } else {
            $regionCollection->addFieldToFilter('country_id', ['eq' => 'LV']);
            foreach ($regionCollection as $region) {
                $regName = str_replace('novads', 'nov.', $region->getName());
                $magentoRegions[$regName] = [
                    'region_id' => $region->getRegionId(),
                    'region_code' => $region->getCode(),
                ];
            }

            foreach ($pickupPointsData as $key => $pickupPointData) {
                if (isset($pickupPointData['area']) && isset($magentoRegions[$pickupPointData['area']])) {
                    $magentoRegionId = $magentoRegions[$pickupPointData['area']]['region_id'];
                    $magentoRegionCode = $magentoRegions[$pickupPointData['area']]['region_code'];
                    $magentoRegionName = $pickupPointData['area'];
                } elseif (isset($magentoRegions[$pickupPointData['city']])) {
                    $magentoRegionId = $magentoRegions[$pickupPointData['city']]['region_id'];
                    $magentoRegionCode = $magentoRegions[$pickupPointData['city']]['region_code'];
                    $magentoRegionName = $pickupPointData['city'];
                }

                if (isset($magentoRegionId) && isset($magentoRegionCode) && isset($magentoRegionName)) {
                    $pickupPointsData[$key]['magento_region_id'] = $magentoRegionId;
                    $pickupPointsData[$key]['magento_region_code'] = $magentoRegionCode;
                    $pickupPointsData[$key]['magento_region_name'] = str_replace('nov.', 'novads', $magentoRegionName);
                }

                $nameForSorting = $pickupPointData['city'] ?? $pickupPointData['village'] ?? $pickupPointData['area'] ?? '';
                $pickupPointsData[$key]['name_for_sorting'] = $nameForSorting;
            }
        }

        usort($pickupPointsData, function ($a, $b) {
            return strnatcmp(
                iconv('utf8', 'US-ASCII//TRANSLIT', $a['name_for_sorting']),
                iconv('utf8', 'US-ASCII//TRANSLIT', $b['name_for_sorting'])
            );
        });

        $json = json_encode($pickupPointsData);
        $fp = $this->filesystemDriver->fileOpen($localFile, 'w');
        $this->filesystemDriver->fileWrite($fp, $json);
        $this->filesystemDriver->fileClose($fp);
        return true;
    }

    /**
     * @throws FileSystemException
     * @throws LocalizedException
     */
    private function updateCountry(string $localFile, string $endPoint)
    {
        $this->curl->get($endPoint);

        if ($this->curl->getStatus() !== 200) {
            $message = new Phrase('Couldn\'t update the "%1" with the endpoint of "%2".', [$localFile, $endPoint]);
            $this->logger->critical($message);
            throw new LocalizedException($message);
        }
        $json = $this->curl->getBody();
        $fp = $this->filesystemDriver->fileOpen($localFile, 'w');
        $this->filesystemDriver->fileWrite($fp, $json);
        $this->filesystemDriver->fileClose($fp);
        return true;
    }

    private function findLithuaniaRegion(string $area)
    {
        $areaShort = strlen($area) > 5
            ? substr($area, 0, -3)
            : $area;
        foreach (self::LITHUANIA_REGIONS as $regionName => $region) {
            foreach ($region as $district) {
                if (str_starts_with($district, $areaShort)) {
                    return $regionName;
                }
            }
        }
        return false;
    }
}
