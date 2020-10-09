<?php

namespace SM\Customer\Model\Data;

/**
 * Class CustomerChangePasswordResult
 * @package SM\Customer\Model\Data
 */
class CustomerChangePasswordResult extends \Magento\Framework\DataObject implements \SM\Customer\Api\Data\CustomerChangePasswordResultInterface
{
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    public function getLocationAppears()
    {
        return $this->getData(self::LOCATION_APPEARS);
    }

    public function setLocationAppears($location)
    {
        return $this->setData(self::LOCATION_APPEARS, $location);
    }
}
