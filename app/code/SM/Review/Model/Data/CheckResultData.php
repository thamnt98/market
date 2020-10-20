<?php


namespace SM\Review\Model\Data;

use Magento\Framework\DataObject;
use SM\Review\Api\Data\CheckResultDataInterface;

/**
 * Class CheckResult
 * @package SM\Review\Model\Data
 */
class CheckResultData extends DataObject implements CheckResultDataInterface
{

    /**
     * @inheritDoc
     */
    public function getIsAllow()
    {
        return $this->getData(self::IS_ALLOW);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setIsAllow($value)
    {
        return $this->setData(self::IS_ALLOW, $value);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId($value)
    {
        return $this->setData(self::ORDER_ID, $value);
    }
}
