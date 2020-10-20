<?php
/**
 * Class CreditMethodsInterface
 * @package SM\Checkout\Api\Data\Checkout\PaymentMethods
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Checkout\Api\Data\Checkout\PaymentMethods;

use SM\Checkout\Api\Data\Checkout\PaymentMethodsInterface;

interface VirtualMethodInterface
{
    const TITLE = 'title';
    const METHODS = 'methods';

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return \SM\Checkout\Api\Data\Checkout\PaymentMethods\PaymentMethodInterface[]
     */
    public function getMethods();
}
