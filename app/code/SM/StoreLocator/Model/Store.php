<?php

namespace SM\StoreLocator\Model;

use SM\StoreLocator\Api\Data\StoreInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class Store
 * @package SM\StoreLocator\Model
 */
class Store extends AbstractExtensibleModel implements StoreInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($value)
    {
        return $this->setData(self::ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreCode()
    {
        return $this->_getData(self::STORE_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreCode($value)
    {
        return $this->setData(self::STORE_CODE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsActive()
    {
        return $this->_getData(self::IS_ACTIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActive($value)
    {
        return $this->setData(self::IS_ACTIVE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress()
    {
        return $this->_getData(self::ADDRESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAddress($value)
    {
        return $this->setData(self::ADDRESS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(\SM\StoreLocator\Api\Data\StoreExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }
}
