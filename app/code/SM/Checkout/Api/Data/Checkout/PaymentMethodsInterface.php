<?php
/**
 * Class PaymentMethodsInterface
 * @package SM\Checkout\Api\Data\Checkout
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Checkout\Api\Data\Checkout;

interface PaymentMethodsInterface
{
    const VIRTUAL = 'virtual';
    const CREDIT = 'credit';

    /**
     * @return \SM\Checkout\Api\Data\Checkout\PaymentMethods\VirtualMethodInterface
     */
    public function getVirtual();

    /**
     * @return \SM\Checkout\Api\Data\Checkout\PaymentMethods\CreditMethodInterface
     */
    public function getCredit();
}
