<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 08 2020
 * Time: 5:44 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model\ResourceModel;

class Sms extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const TABLE_NAME = 'sm_notification_sms';

    /**
     * Initialize resource
     */
    public function _construct()
    {
        $this->_init(self::TABLE_NAME, 'message_id');
    }
}
