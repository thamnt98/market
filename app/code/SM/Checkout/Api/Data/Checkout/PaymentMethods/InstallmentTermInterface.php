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

use SM\Checkout\Model\Api\CheckoutData\PaymentMethods\InstallmentTerm;

interface InstallmentTermInterface
{
    const LABEL = 'label';
    const VALUE = 'value';
    const SERVICE_FEE = 'serviceFee';
    const SERVICE_FEE_AMOUNT = 'serviceFeeAmount';
    const SERVICE_FEE_VALUE = 'serviceFeeValue';
    const TOTAL_FEE = 'totalFee';

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param $value
     * @return $this
     */
    public function setLabel($value);

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value);

    /**
     * @return string
     */
    public function getServiceFee();

    /**
     * @param $value
     * @return $this
     */
    public function setServiceFee($value);

    /**
     * @return string
     */
    public function getServiceFeeAmount();

    /**
     * @param $value
     * @return $this
     */
    public function setServiceFeeAmount($value);

    /**
     * @return string
     */
    public function getServiceFeeValue();

    /**
     * @param $value
     * @return $this
     */
    public function setServiceFeeValue($value);

    /**
     * @return string
     */
    public function getTotalFee();

    /**
     * @param $value
     * @return $this
     */
    public function setTotalFee($value);

    /**
     * @return string
     */
    public function getTotalFeePerMonth();

    /**
     * @param $data
     * @return $this
     */
    public function setTotalFeePerMonth($data);
}
