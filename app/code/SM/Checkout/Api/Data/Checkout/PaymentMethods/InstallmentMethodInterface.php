<?php
/**
 * Class InstallmentMethodInterface
 * @package SM\Checkout\Api\Data\Checkout\PaymentMethods
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Checkout\Api\Data\Checkout\PaymentMethods;

interface InstallmentMethodInterface extends PaymentMethodInterface
{
    const INSTALLMENT_TERM = 'installment_term';

    /**
     * @return \SM\Checkout\Api\Data\Checkout\PaymentMethods\InstallmentTermInterface[]
     */
    public function getInstallmentTerm();
}
