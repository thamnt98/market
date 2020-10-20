<?php
/**
 * Class InstallmentMethod
 * @package SM\Checkout\Model\Api\CheckoutData\PaymentMethods
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Checkout\Model\Api\CheckoutData\PaymentMethods;

use SM\Checkout\Api\Data\Checkout\PaymentMethods\InstallmentMethodInterface;

class InstallmentMethod extends PaymentMethod implements InstallmentMethodInterface
{
    /**
     * @inheritDoc
     */
    public function getInstallmentTerm()
    {
        return $this->_get(self::INSTALLMENT_TERM);
    }

    /**
     * @param $data
     * @return $this
     */
    public function setInstallmentTerm($data)
    {
        return $this->setData(self::INSTALLMENT_TERM, $data);
    }
}
