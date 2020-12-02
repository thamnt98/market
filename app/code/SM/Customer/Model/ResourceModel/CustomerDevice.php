<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Customer
 *
 * Date: June, 08 2020
 * Time: 1:53 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Customer\Model\ResourceModel;

class CustomerDevice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const TABLE_NAME = 'sm_customer_devices';

    /**
     * Initialize resource
     */
    public function _construct()
    {
        $this->_init(self::TABLE_NAME, 'id');
    }

    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (
            (
                $object->getData('token') &&
                $this->isNewMobileDevice($object->getData('device_id'), $object->getData('customer_id'))
            ) ||
            (
                !$object->getData('token') &&
                $this->isNewIP($object->getData('device_id'), $object->getData('customer_id'))
            )
        ) {
            $object->setData(\SM\Customer\Model\CustomerDevice::NEW_DEVICE_KEY, true);
        }

        if ($object->getData('token')) {
            $this->removeDuplicateToken($object->getData('device_id'), $object->getData('token'));
        }

        return parent::_beforeSave($object);
    }

    /**
     * @param string $deviceId
     * @param string $token
     */
    protected function removeDuplicateToken($deviceId, $token)
    {
        $this->getConnection()
            ->delete(
                self::TABLE_NAME,
                "token = '{$token}' OR device_id = '{$deviceId}'"
            );
    }

    /**
     * @param string $deviceId
     * @param int    $customerId
     *
     * @return bool
     */
    protected function isNewMobileDevice($deviceId, $customerId)
    {
        $select = $this->getConnection()->select();
        $select->from(self::TABLE_NAME, 'COUNT(id)')
            ->where('type <> ?', \SM\Customer\Model\CustomerDevice::DESKTOP_TYPE)
            ->where('customer_id = ?', $customerId);

        if (!$this->getConnection()->fetchOne($select)) { // First device
            return false;
        }

        $select->where('device_id = ?', $deviceId);

        return !$this->getConnection()->fetchOne($select);
    }

    /**
     * @param string $ip
     * @param int    $customerId
     *
     * @return bool
     */
    protected function isNewIP($ip, $customerId)
    {
        $select = $this->getConnection()->select();
        $select->from(self::TABLE_NAME, 'COUNT(id)')
            ->where('customer_id = ?', $customerId)
            ->where('type = ?', \SM\Customer\Model\CustomerDevice::DESKTOP_TYPE);

        if (!$this->getConnection()->fetchOne($select)) { // First IP
            return false;
        }

        $select->where('device_id = ?', $ip);

        return !$this->getConnection()->fetchOne($select);
    }
}
