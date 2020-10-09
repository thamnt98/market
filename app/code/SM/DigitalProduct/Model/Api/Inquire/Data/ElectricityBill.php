<?php
/**
 * Class ElectricityBill
 * @package SM\DigitalProduct\Model\Api\Inquire\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Model\Api\Inquire\Data;

use SM\DigitalProduct\Api\Inquire\Data\ElectricityBillInterface;

class ElectricityBill extends AbstractElectricity implements ElectricityBillInterface
{
    /**
     * @inheritDoc
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function getPower()
    {
        return $this->getData(self::POWER);
    }

    /**
     * @inheritDoc
     */
    public function getPeriod()
    {
        return $this->getData(self::PERIOD);
    }

    /**
     * @inheritDoc
     */
    public function getBill()
    {
        return $this->getData(self::BILL);
    }

    /**
     * @inheritDoc
     */
    public function getPenalty()
    {
        return $this->getData(self::PENALTY);
    }

    /**
     * @inheritDoc
     */
    public function getIncentiveAndTaxFee()
    {
        return $this->getData(self::INCENTIVE_AND_TAX_FEE);
    }
}
