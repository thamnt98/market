<?php


namespace SM\DigitalProduct\Model\Data\Inquire;

use Magento\Framework\DataObject;
use SM\DigitalProduct\Api\Data\Inquire\PdamDetailUsageDataInterface;

/**
 * Class PdamDetailUsageData
 * @package SM\DigitalProduct\Model\Data\Inquire
 */
class PdamDetailUsageData extends DataObject implements PdamDetailUsageDataInterface
{

    /**
     * @inheritDoc
     */
    public function getUsage1()
    {
        return $this->getData(self::USAGE1);
    }

    /**
     * @inheritDoc
     */
    public function getUsage2()
    {
        return $this->getData(self::USAGE2);
    }

    /**
     * @inheritDoc
     */
    public function getUsage3()
    {
        return $this->getData(self::USAGE3);
    }

    /**
     * @inheritDoc
     */
    public function getUsage4()
    {
        return $this->getData(self::USAGE4);
    }

    /**
     * @inheritDoc
     */
    public function setUsage1($value)
    {
        return $this->setData(self::USAGE1, $value);
    }

    /**
     * @inheritDoc
     */
    public function setUsage2($value)
    {
        return $this->setData(self::USAGE2, $value);
    }

    /**
     * @inheritDoc
     */
    public function setUsage3($value)
    {
        return $this->setData(self::USAGE3, $value);
    }

    /**
     * @inheritDoc
     */
    public function setUsage4($value)
    {
        return $this->setData(self::USAGE4, $value);
    }
}
