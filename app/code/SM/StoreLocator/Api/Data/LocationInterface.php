<?php

namespace SM\StoreLocator\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface LocationInterface
 * @package SM\StoreLocator\Api\Data
 */
interface LocationInterface extends ExtensibleDataInterface
{
    const ID = 'place_id';
    const NAME = 'name';
    const STORE_CODE = 'store_code';
    const IS_ACTIVE = 'is_active';
    const ADDRESS = 'address';

    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $id
     *
     * @return \SM\StoreLocator\Api\Data\LocationInterface
     */
    public function setId($id);

    /**
     * Get store name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set store name
     *
     * @param string $value
     * @return $this
     */
    public function setName($value);

    /**
     * Get store code
     *
     * @return string|null
     */
    public function getStoreCode();

    /**
     * Set store code
     *
     * @param string $value
     * @return $this
     */
    public function setStoreCode($value);


    /**
     * Get is active
     *
     * @return bool|null
     */
    public function getIsActive();

    /**
     * Set is active
     *
     * @param bool $value
     * @return $this
     */
    public function setIsActive($value);

    /**
     * @return \SM\StoreLocator\Api\Data\StoreAddressInterface
     */
    public function getAddress();

    /**
     * @param \SM\StoreLocator\Api\Data\StoreAddressInterface $value
     * @return $this
     */
    public function setAddress($value);

    /**
     * @param \SM\StoreLocator\Api\Data\LocationExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\SM\StoreLocator\Api\Data\LocationExtensionInterface $extensionAttributes);

    /**
     * @return \SM\StoreLocator\Api\Data\LocationExtensionInterface
     */
    public function getExtensionAttributes();
}
