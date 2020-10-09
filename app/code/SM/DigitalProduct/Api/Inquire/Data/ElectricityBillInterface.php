<?php
/**
 * Class ElectricityBillInterface
 * @package SM\DigitalProduct\Api\Data\Inquire\Electricity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Api\Inquire\Data;

interface ElectricityBillInterface extends ResponseDataInterface
{
    const CUSTOMER_ID = 'customer_id';
    const NAME = 'subscriber_name';
    const POWER = 'power';
    const PERIOD = 'period';
    const BILL = 'bill';
    const PENALTY = 'penalty';
    const INCENTIVE_AND_TAX_FEE = 'value_added_tax';

    /**
     * @return string
     */
    public function getCustomerId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getPower();

    /**
     * @return string
     */
    public function getPeriod();

    /**
     * @return int
     */
    public function getBill();

    /**
     * @return int
     */
    public function getPenalty();

    /**
     * @return int
     */
    public function getIncentiveAndTaxFee();
}
