<?php
/**
 * Class Product
 * @package SM\DigitalProduct\Model\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Model\Data;

use SM\DigitalProduct\Api\Data\ProductInterface;

class Product extends \Magento\Catalog\Model\Product implements \SM\DigitalProduct\Api\Data\ProductInterface
{
    /**
     * @inheritDoc
     */
    public function getDenom()
    {
        return $this->_getData(self::DENOME);
    }

    /**
     * @inheritDoc
     */
    public function setDenom($value)
    {
        $this->setData(self::DENOME, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProductIdVendor()
    {
        return $this->_getData(self::PRODUCT_ID_VENDOR);
    }

    /**
     * @inheritDoc
     */
    public function setProductIdVendor(int $value)
    {
        $this->setData(self::PRODUCT_ID_VENDOR, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->_getData(self::DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setDescription($value)
    {
        $this->setData(self::DESCRIPTION, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSpecialPrice($value)
    {
        $this->setData(self::SPECIAL_PRICE, $value);
    }
}
