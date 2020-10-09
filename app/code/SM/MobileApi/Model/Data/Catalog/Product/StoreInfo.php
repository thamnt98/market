<?php

namespace SM\MobileApi\Model\Data\Catalog\Product;

use Magento\Framework\Model\AbstractExtensibleModel;
use SM\MobileApi\Api\Data\Catalog\Product\StoreInfoInterface;

/**
 * Class StoreInfo
 * @package SM\MobileApi\Model\Data\Catalog\Product
 */
class StoreInfo extends AbstractExtensibleModel implements StoreInfoInterface
{
    /**
     * @return mixed|string|null
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @param string $name
     * @return StoreInfo
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @return mixed|string|null
     */
    public function getRegion()
    {
        return $this->getData(self::REGION);
    }

    /**
     * @param string $region
     * @return StoreInfo
     */
    public function setRegion($region)
    {
        return $this->setData(self::REGION, $region);
    }

    /**
     * @return mixed|string|null
     */
    public function getCity()
    {
        return $this->getData(self::CITY);
    }

    /**
     * @param string $city
     * @return StoreInfo
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * @return mixed|string|null
     */
    public function getStreet()
    {
        return $this->getData(self::STREET);
    }

    /**
     * @param string $street
     * @return StoreInfo
     */
    public function setStreet($street)
    {
        return $this->setData(self::STREET, $street);
    }

    /**
     * @return mixed|string|null
     */
    public function getPostcode()
    {
        return $this->getData(self::POSTCODE);
    }

    /**
     * @param string $postcode
     * @return StoreInfo
     */
    public function setPostcode($postcode)
    {
        return $this->setData(self::POSTCODE, $postcode);
    }

    /**
     * @return mixed|string|null
     */
    public function getOpenUntil()
    {
        return $this->getData(self::OPEN_UNTIL);
    }

    /**
     * @param string $time
     * @return StoreInfo
     */
    public function setOpenUntil($time)
    {
        return $this->setData(self::OPEN_UNTIL, $time);
    }

    /**
     * @inheritDoc
     */
    public function getPickUpTime()
    {
        return $this->getData(self::PICK_UP_TIME);
    }

    /**
     * @inheritDoc
     */
    public function setPickUpTime($time)
    {
        return $this->setData(self::PICK_UP_TIME, $time);
    }

    /**
     * @inheritDoc
     */
    public function getPickUpDate()
    {
        return $this->getData(self::PICK_UP_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setPickUpDate($value)
    {
        return $this->setData(self::PICK_UP_DATE, $value);
    }
}
