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
        if ($this->isNewDevice($object->getData('device_id'), $object->getData('customer_id'))) {
            if ($object->getData('type') !== \SM\Customer\Model\CustomerDevice::DESKTOP_TYPE) {
                $object->setData(\SM\Customer\Model\CustomerDevice::NEW_DEVICE_KEY, true);
            }
        }

        if ($object->getData('token')) {
            $this->removeDuplicateToken($object);
        }

        return parent::_beforeSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     */
    protected function removeDuplicateToken(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->getConnection()
            ->update(
                self::TABLE_NAME,
                ['status' => 0],
                "device_id = '{$object->getData('device_id')}'"
            );
        $this->getConnection()
            ->delete(
                self::TABLE_NAME,
                "customer_id = {$object->getData('customer_id')} AND device_id = '{$object->getData('device_id')}'"
            );
    }

    /**
     * @param string $deviceId
     * @param int    $customerId
     *
     * @return bool
     */
    public function isNewDevice($deviceId, $customerId)
    {
        if ($this->isFirstDevice($customerId)) {
            return true;
        } else {
            $select = $this->getConnection()->select();
            $select->from(self::TABLE_NAME, 'COUNT(id)')
                ->where('customer_id = ?', $customerId)
                ->where('device_id = ?', $deviceId);

            return !$this->getConnection()->fetchOne($select);
        }
    }

    /**
     * @param int $customerId
     *
     * @return bool
     */
    public function isFirstDevice($customerId)
    {
        $select = $this->getConnection()->select();
        $select->from(self::TABLE_NAME, 'COUNT(id)')
            ->where('customer_id = ?', $customerId);

        return !$this->getConnection()->fetchOne($select);
    }
}
