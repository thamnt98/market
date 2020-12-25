<?php
/**
 * Class FormInfo
 * @package SM\Sales\Api\Data\Creditmemo
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Sales\Api\Data\Creditmemo;

use \SM\Sales\Model\Creditmemo\RequestFormData;

interface FormInformationInterface
{
    const BANKS = 'banks';
    const IS_SUBMITTED = 'is_submitted';
    const SUBMITTED_VALUE = 2;
    const TOTAL_REFUND = RequestFormData::TOTAL_REFUND_KEY;
    const REFERENCE_NUMBER = RequestFormData::ORDER_REFERENCE_NUMBER_KEY;
    const ORDER_ID = 'order_id';

    /**
     * @return \SM\Sales\Api\Data\Creditmemo\BankInterface[]
     */
    public function getBanks();

    /**
     * @return bool
     */
    public function getIsSubmitted();

    /**
     * @return int
     */
    public function getTotalRefund();

    /**
     * @return string
     */
    public function getReferenceNumber();

    /**
     * @return int
     */
    public function getOrderId();
}
