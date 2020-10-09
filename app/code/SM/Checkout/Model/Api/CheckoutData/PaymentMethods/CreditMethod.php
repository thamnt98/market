<?php
/**
 * Class CreditMethod
 * @package SM\Checkout\Model\Api\CheckoutData\PaymentMethods
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */


namespace SM\Checkout\Model\Api\CheckoutData\PaymentMethods;

use SM\Checkout\Api\Data\Checkout\PaymentMethods\CreditMethodInterface;

class CreditMethod extends VirtualMethod implements CreditMethodInterface
{
    /**
     * @inheritDoc
     */
    public function getInstallmentMethods()
    {
        return $this->_get(self::INSTALLMENT_METHODS);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setInstallmentMethods($value)
    {
        return $this->setData(self::INSTALLMENT_METHODS, $value);
    }
}
