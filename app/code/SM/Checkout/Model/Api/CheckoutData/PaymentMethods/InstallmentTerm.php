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

use SM\Checkout\Api\Data\Checkout\PaymentMethods\InstallmentTermInterface;

class InstallmentTerm extends PaymentMethod implements InstallmentTermInterface
{
    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setLabel($value)
    {
        return $this->setData(self::LABEL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getServiceFee()
    {
        return $this->getData(self::SERVICE_FEE);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setServiceFee($value)
    {
        return $this->setData(self::SERVICE_FEE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getServiceFeeAmount()
    {
        return $this->getData(self::SERVICE_FEE_AMOUNT);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setServiceFeeAmount($value)
    {
        return $this->setData(self::SERVICE_FEE_AMOUNT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getServiceFeeValue()
    {
        return $this->getData(self::SERVICE_FEE_VALUE);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setServiceFeeValue($value)
    {
        return $this->setData(self::SERVICE_FEE_VALUE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getTotalFee()
    {
        return $this->getData(self::TOTAL_FEE);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setTotalFee($value)
    {
        return $this->setData(self::TOTAL_FEE, $value);
    }

    public function getTotalFeePerMonth()
    {
        return $this->getData('total_fee_per_month');
    }

    public function setTotalFeePerMonth($data)
    {
        return $this->setData('total_fee_per_month',$data);
    }


}
