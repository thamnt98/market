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

namespace SM\Notification\Model\ResourceModel\CustomerMessage;

use SM\Notification\Model\CustomerMessage as Model;
use SM\Notification\Model\ResourceModel\CustomerMessage as ResourceModel;
use SM\Notification\Model\ResourceModel\Notification;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->joinInner(
            [ResourceModel::MESSAGE_JOIN_TABLE_ALIAS => Notification::TABLE_NAME],
            'main_table.message_id = ' . ResourceModel::MESSAGE_JOIN_TABLE_ALIAS . '.id',
            [
                'content',
                'created_at',
                'end_date',
                'event',
                'sub_event',
                'image',
                'params',
                'redirect_id',
                'redirect_type',
                'start_date',
                'title'
            ]
        );

        return $this;
    }
}
