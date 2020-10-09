<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 07 2020
 * Time: 6:51 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model\ResourceModel;

class CustomerMessage extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const TABLE_NAME                = 'sm_notification_customer';
    const MESSAGE_JOIN_TABLE_ALIAS  = 'message';
    const WEB_TYPE_JOIN_TABLE_ALIAS = 'web';

    /**
     * Initialize resource
     */
    public function _construct()
    {
        $this->_init(self::TABLE_NAME, 'id');
    }

    /**
     * @override
     *
     * @param string                                 $field
     * @param mixed                                  $value
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->joinInner(
            [self::MESSAGE_JOIN_TABLE_ALIAS => Notification::TABLE_NAME],
            'message_id = ' . self::MESSAGE_JOIN_TABLE_ALIAS . '.id',
            [
                'created_at',
                'end_date',
                'event',
                'redirect_id',
                'redirect_type',
                'start_date',
                'title',
                'content',
                'image',
                'params',
            ]
        );

        return $select;
    }

    /**
     * @param array $ids
     *
     * @return int
     */
    public function read($ids = [])
    {
        if (!is_array($ids) || count($ids) < 1) {
            return 0;
        }

        $conn = $this->getConnection();

        return $conn->update(
            self::TABLE_NAME,
            ['is_read' => 1],
            "id IN ('" . implode("','", $ids) . "')"
        );
    }

    /**
     * @param array $ids
     *
     * @return int
     */
    public function unRead($ids = [])
    {
        if (!is_array($ids) || count($ids) < 1) {
            return 0;
        }

        $conn = $this->getConnection();

        return $conn->update(
            self::TABLE_NAME,
            ['is_read' => 0],
            "id IN ('" . implode("','", $ids) . "')"
        );
    }
}
