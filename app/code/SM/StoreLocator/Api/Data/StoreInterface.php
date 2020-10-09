<?php

namespace SM\StoreLocator\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface StoreInterface
 * @package SM\StoreLocator\Api\Data
 */
interface StoreInterface extends ExtensibleDataInterface
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
     * @return $this
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
     * @return \SM\StoreLocator\Api\Entity\StoreAddressInterface
     */
    public function getAddress();

    /**
     * @param \SM\StoreLocator\Api\Entity\StoreAddressInterface $value
     * @return $this
     */
    public function setAddress($value);

    /**
     * @param \SM\StoreLocator\Api\Data\StoreExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\SM\StoreLocator\Api\Data\StoreExtensionInterface $extensionAttributes);

    /**
     * @return \SM\StoreLocator\Api\Data\StoreExtensionInterface
     */
    public function getExtensionAttributes();
}
