<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Controller\Electricity
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Block\Electricity\Bill;

use SM\DigitalProduct\Api\Data\Inquire\InquireElectricityPostPaidDataInterface;
use SM\DigitalProduct\Block\AbstractProductList;

/**
 * Class CustomerInformation
 * @package SM\DigitalProduct\Controller\Electricity
 */
class CustomerInformation extends AbstractProductList
{
    /**
     * @var InquireElectricityPostPaidDataInterface
     */
    private $information;

    private $periods;

    private $totalBill;

    private $penaltyFee;

    private $incentive;

    /**
     * @return InquireElectricityPostPaidDataInterface
     */
    public function getInformation()
    {
        return $this->information;
    }

    /**
     * @param InquireElectricityPostPaidDataInterface $value
     * @return $this
     */
    public function setInformation($value)
    {
        $this->information = $value;
        return $this;
    }

    /**
     * @param \SM\DigitalProduct\Api\Data\Inquire\ElectricityPostPaidBillDataInterface[] $bills
     */
    public function calculateMultipleBills($bills)
    {
        $this->totalBill = 0;
        $this->penaltyFee = 0;
        $this->incentive = 0;
        foreach ($bills as $bill) {
            $this->totalBill += floatval($bill->getTotalElectricityBill());
            $this->penaltyFee += floatval($bill->getPenaltyFee());
            $this->incentive += floatval($bill->getIncentive());
        }
        $this->periods = $this->formatPeriodRange($bills);
    }

    /**
     * @param \SM\DigitalProduct\Api\Data\Inquire\ElectricityPostPaidBillDataInterface[] $bills
     * @return string
     */
    public function formatPeriodRange($bills)
    {
        $first = reset($bills);
        if (count($bills) > 1) {
            $last = end($bills);
            return $first->getBillPeriod() . "-" . $last->getBillPeriod();
        }
        return $first->getBillPeriod();
    }

    public function getPeriod()
    {
        return $this->periods;
    }

    public function getTotalBill()
    {
        return $this->totalBill;
    }

    public function getPenaltyFee()
    {
        return $this->penaltyFee;
    }

    public function getIncentive()
    {
        return $this->incentive;
    }
}
