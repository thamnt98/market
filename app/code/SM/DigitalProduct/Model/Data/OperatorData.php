<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Model\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Model\Data;

use Magento\Framework\DataObject;
use SM\DigitalProduct\Api\Data\OperatorDataInterface;

/**
 * Class OperatorData
 * @package SM\DigitalProduct\Model\Data
 */
class OperatorData extends DataObject implements OperatorDataInterface
{

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function getBrandId()
    {
        return $this->getData(self::BRAND_ID);
    }

    /**
     * @inheritDoc
     */
    public function getOperatorName()
    {
        return $this->getData(self::OPERATOR_NAME);
    }

    /**
     * @inheritDoc
     */
    public function getServiceName()
    {
        return $this->getData(self::SERVICE_NAME);
    }

    /**
     * @inheritDoc
     */
    public function getPrefixNumber()
    {
        return $this->getData(self::PREFIX_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function setId($value)
    {
        return $this->setData(self::ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function setBrandId($value)
    {
        return $this->setData(self::BRAND_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function setOperatorName($value)
    {
        return $this->setData(self::OPERATOR_NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function setServiceName($value)
    {
        return $this->setData(self::SERVICE_NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function setPrefixNumber($value)
    {
        return $this->setData(self::PREFIX_NUMBER, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOperatorIcon()
    {
        return $this->getData(self::OPERATOR_ICON);
    }

    /**
     * @inheritDoc
     */
    public function setOperatorIcon($value)
    {
        return $this->setData(self::OPERATOR_ICON, $value);
    }

    /**
     * @inheritDoc
     */
    public function setProducts($value)
    {
        return $this->setData(self::PRODUCTS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getProducts()
    {
        return $this->getData(self::PRODUCTS);
    }
}
