<?php
/**
 * Class VirtualMethod
 * @package SM\Checkout\Model\Api\CheckoutData\PaymentMethods
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Checkout\Model\Api\CheckoutData\PaymentMethods;

use Magento\Framework\Api\AbstractSimpleObject;
use SM\Checkout\Api\Data\Checkout\PaymentMethods\VirtualMethodInterface;

class VirtualMethod extends AbstractSimpleObject implements VirtualMethodInterface
{
    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }


    /**
     * @param $value
     * @return $this
     */
    public function setTitle($value)
    {
        return $this->setData(self::TITLE, $value);
    }


    /**
     * @inheritDoc
     */
    public function getMethods()
    {
        return $this->_get(self::METHODS);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setMethods($value)
    {
        return $this->setData(self::METHODS, $value);
    }
}
