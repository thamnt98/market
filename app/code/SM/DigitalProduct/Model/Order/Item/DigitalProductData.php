<?php
/**
 * Class DigitalProduct
 * @package SM\DigitalProduct\Model\Order\Item
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Model\Order\Item;

use SM\DigitalProduct\Model\Cart\Data\AbstractDataObject;
use SM\DigitalProduct\Api\Data\Order\DigitalProductInterface;

class DigitalProductData extends AbstractDataObject implements DigitalProductInterface
{
    /**
     * @inheritDoc
     */
    public function getServiceType()
    {
        return $this->_get(self::SERVICE_TYPE);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setServiceType($value)
    {
        return $this->setData(self::SERVICE_TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDigital()
    {
        return $this->_get(self::DIGITAL);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setDigital($value)
    {
        return $this->setData(self::DIGITAL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDigitalTransaction()
    {
        return $this->_get(self::DIGITAL_TRANSACTION);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setDigitalTransaction($value)
    {
        return $this->setData(self::DIGITAL_TRANSACTION, $value);
    }
}
