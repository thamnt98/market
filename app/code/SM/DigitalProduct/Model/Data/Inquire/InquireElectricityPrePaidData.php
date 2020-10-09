<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Model\Data
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Model\Data\Inquire;

use SM\DigitalProduct\Api\Data\Inquire\InquireElectricityPrePaidDataInterface;

/**
 * Class InquireElectricityPrePaidData
 * @package SM\DigitalProduct\Model\Data
 */
class InquireElectricityPrePaidData extends InquireElectricityData implements InquireElectricityPrePaidDataInterface
{

    /**
     * @inheritDoc
     */
    public function setPlnRefno($value)
    {
        return $this->setData(self::PLN_REFNO, $value);
    }

    /**
     * @inheritDoc
     */
    public function setDistributionCode($value)
    {
        return $this->setData(self::DISTRIBUTION_CODE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setServiceUnit($value)
    {
        return $this->setData(self::SERVICE_UNIT, $value);
    }

    /**
     * @inheritDoc
     */
    public function setMaxKwhUnit($value)
    {
        return $this->setData(self::MAX_KWH_UNIT, $value);
    }

    /**
     * @inheritDoc
     */
    public function setTotalRepeat($value)
    {
        return $this->setData(self::TOTAL_REPEAT, $value);
    }

    /**
     * @inheritDoc
     */
    public function setPowerPurchaseUnsold($value)
    {
        return $this->setData(self::POWER_PURCHASE_UNSOLD, $value);
    }

    /**
     * @inheritDoc
     */
    public function setPowerPurchaseUnsold2($value)
    {
        return $this->setData(self::POWER_PURCHASE_UNSOLD2, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPlnRefno()
    {
        return $this->getData(self::PLN_REFNO);
    }

    /**
     * @inheritDoc
     */
    public function getDistributionCode()
    {
        return $this->getData(self::DISTRIBUTION_CODE);
    }

    /**
     * @inheritDoc
     */
    public function getServiceUnit()
    {
        return $this->getData(self::SERVICE_UNIT);
    }

    /**
     * @inheritDoc
     */
    public function getMaxKwhUnit()
    {
        return $this->getData(self::MAX_KWH_UNIT);
    }

    /**
     * @inheritDoc
     */
    public function getTotalRepeat()
    {
        return $this->getData(self::TOTAL_REPEAT);
    }

    /**
     * @inheritDoc
     */
    public function getPowerPurchaseUnsold()
    {
        return $this->getData(self::POWER_PURCHASE_UNSOLD);
    }

    /**
     * @inheritDoc
     */
    public function getPowerPurchaseUnsold2()
    {
        return $this->getData(self::POWER_PURCHASE_UNSOLD2);
    }

    /**
     * @inheritDoc
     */
    public function setServiceUnitPhone($value)
    {
        return $this->setData(self::SERVICE_UNIT_PHONE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getServiceUnitPhone()
    {
        return $this->getData(self::SERVICE_UNIT_PHONE);
    }
}
