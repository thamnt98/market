<?php
/**
 * Class FormInterface
 * @package SM\Sales\Api\Data\Creditmemo
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Sales\Api\Data\Creditmemo;

interface FormInterface
{
    /**
     * @return string
     */
    public function getBank();

    /**
     * @return int
     */
    public function getAccountNo();

    /**
     * @return string
     */
    public function getAccountName();

    /**
     * @return int
     */
    public function getTotalRefund();

    /**
     * @return string
     */
    public function getReferenceNumber();

    /**
     * @return string
     */
    public function getPaymentMethod();

    /**
     * @return string
     */
    public function getCreditmemoId();

    /**
     * @return string
     */
    public function getPaymentNumber();
}
