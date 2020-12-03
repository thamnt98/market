<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Customer
 *
 * Date: June, 08 2020
 * Time: 2:39 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Customer\Model\Data;

class CustomerDevice extends \Magento\Framework\DataObject implements \SM\Customer\Api\Data\CustomerDeviceInterface
{
    /**
     * @return int
     */
    public function getCustomerId()
    {
        return (int) $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @param int $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        $this->setData(self::CUSTOMER_ID, (int) $customerId);

        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->getData(self::TOKEN);
    }

    /**
     * @param string $token
     *
     * @return $this
     */
    public function setToken($token)
    {
        $this->setData(self::TOKEN, $token);

        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(self::CREATED_AT, $createdAt);

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->setData(self::TYPE, $type);

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int) $this->getData(self::ID);
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->setData(self::ID, (int) $id);

        return $this;
    }

    /**
     * @return string
     */
    public function getDeviceId()
    {
        return $this->getData(self::DEVICE_ID);
    }

    /**
     * @param string $deviceId
     *
     * @return $this
     */
    public function setDeviceId($deviceId)
    {
        $this->setData(self::DEVICE_ID, $deviceId);

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return (int) $this->getData(self::STATUS);
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->setData(self::STATUS, (int) $status);

        return $this;
    }
}
