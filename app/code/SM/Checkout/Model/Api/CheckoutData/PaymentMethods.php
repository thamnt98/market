<?php
/**
 * Class PaymentMethods
 * @package SM\Checkout\Model\Api\CheckoutData
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Checkout\Model\Api\CheckoutData;

use Magento\Framework\Api\AbstractSimpleObject;
use SM\Checkout\Api\Data\Checkout\PaymentMethodsInterface;

class PaymentMethods extends AbstractSimpleObject implements PaymentMethodsInterface
{
    /**
     * @inheritDoc
     */
    public function getVirtual()
    {
        return $this->_get(self::VIRTUAL);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setVirtual($value)
    {
        return $this->setData(self::VIRTUAL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCredit()
    {
        return $this->_get(self::CREDIT);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setCredit($value)
    {
        return $this->setData(self::CREDIT, $value);
    }
}
