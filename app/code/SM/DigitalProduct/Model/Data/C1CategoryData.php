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
use SM\DigitalProduct\Api\Data\C1CategoryDataInterface;

/**
 * Class C1CategoryData
 * @package SM\DigitalProduct\Model\Data
 */
class C1CategoryData extends DataObject implements C1CategoryDataInterface
{

    /**
     * @inheritDoc
     */
    public function getProducts()
    {
        return $this->getData(self::PRODUCTS);
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
    public function getMagentoCategoryId()
    {
        return $this->getData(self::MAGENTO_CATEGORY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setMagentoCategoryId($value)
    {
        return $this->setData(self::MAGENTO_CATEGORY_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType($value)
    {
        return $this->setData(self::TYPE, $value);
    }
}
