<?php

//declare(strict_types=1);

namespace SM\StoreLocator\Model\Store;

use Magento\Framework\Exception\NoSuchEntityException;
use SM\StoreLocator\Api\Data\StoreExtensionFactory;
use SM\StoreLocator\Api\Data\StoreInterface;
use SM\StoreLocator\Api\Data\StoreInterfaceFactory;
use SM\StoreLocator\Api\Entity\StoreAddressInterface;
use SM\StoreLocator\Api\Entity\StoreAddressInterfaceFactory;
use SM\StoreLocator\Model\Store\Attributes\Preparator;

/**
 * Class Converter
 * @package SM\StoreLocator\Model\Store
 */
class Converter
{
    const STATUS = 'status';
    const ADDRESS_LINE_1 = 'address_line_1';
    const ADDRESS_LINE_2 = 'address_line_2';
    const DISTRICT_ID = 'district_id';
    const CITY = 'city';
    const POSTAL_CODE = 'postal_code';
    const COUNTRY_CODE = 'country_code';
    const MAIN_PHONE = 'main_phone';
    const LATITUDE = 'lat';
    const LONGITUDE = 'long';

    /**
     * @var StoreInterfaceFactory
     */
    protected $storeInterfaceFactory;

    /**
     * @var StoreAddressInterfaceFactory
     */
    protected $storeAddressInterfaceFactory;

    /**
     * @var StoreExtensionFactory
     */
    protected $storeExtensionFactory;

    /**
     * @var Preparator
     */
    protected $preparatorOpeningHours;

    /**
     * Converter constructor.
     * @param StoreInterfaceFactory $storeInterfaceFactory
     * @param StoreAddressInterfaceFactory $storeAddressInterfaceFactory
     * @param StoreExtensionFactory $storeExtensionFactory
     * @param Preparator $preparatorOpeningHours
     * @codeCoverageIgnore
     */
    public function __construct(
        StoreInterfaceFactory $storeInterfaceFactory,
        StoreAddressInterfaceFactory $storeAddressInterfaceFactory,
        StoreExtensionFactory $storeExtensionFactory,
        Preparator $preparatorOpeningHours
    ) {
        $this->storeInterfaceFactory = $storeInterfaceFactory;
        $this->storeAddressInterfaceFactory = $storeAddressInterfaceFactory;
        $this->storeExtensionFactory = $storeExtensionFactory;
        $this->preparatorOpeningHours = $preparatorOpeningHours;
    }

    /**
     * @param Location $location
     * @return StoreInterface
     */
    public function convertItem(Location $location): StoreInterface
    {
        return $this->convert($location);
    }

    /**
     * @param Location[] $locations
     * @return StoreInterface[]
     */
    public function convertItems(array $locations): array
    {
        $stores = [];
        foreach ($locations as $location) {
            $stores[] = $this->convert($location);
        }

        return $stores;
    }

    /**
     * @param Location $location
     * @return StoreInterface
     * @codeCoverageIgnore
     */
    protected function convert(Location $location): StoreInterface
    {
        /** @var StoreInterface $store */
        $store = $this->storeInterfaceFactory->create();

        $store->setId($location->getId());
        $store->setName($location->getData(StoreInterface::NAME));
        $store->setStoreCode($location->getData(StoreInterface::STORE_CODE));
        $store->setIsActive($location->getData(self::STATUS));

        $store->setAddress($this->convertAddress($location));

        $storeExtension = $store->getExtensionAttributes() ?? $this->storeExtensionFactory->create();
        $openingHours = $this->preparatorOpeningHours->prepareOpeningHours($location->getData('opening_hours'));
        $storeExtension->setOpeningHours($openingHours);
        $store->setExtensionAttributes($storeExtension);

        return $store;
    }

    /**
     * @param Location $location
     * @return StoreAddressInterface
     * @codeCoverageIgnore
     */
    protected function convertAddress(Location $location): StoreAddressInterface
    {
        /** @var StoreAddressInterface $storeAddress */
        $storeAddress = $this->storeAddressInterfaceFactory->create();

        $storeAddress->setAddressLine1($location->getData(self::ADDRESS_LINE_1));
        $storeAddress->setAddressLine2($location->getData(self::ADDRESS_LINE_2));

        $storeAddress->setCity($location->getData(self::CITY));
        $storeAddress->setDistrictId($location->getData(self::DISTRICT_ID));

        $storeAddress->setLatitude($location->getData(self::LATITUDE));
        $storeAddress->setLongitude($location->getData(self::LONGITUDE));

        return $storeAddress;
    }
}
