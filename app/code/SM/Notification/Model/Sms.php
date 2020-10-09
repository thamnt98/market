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

namespace SM\Notification\Model;

class Sms extends \Magento\Framework\Model\AbstractModel
{
    const CONSUMER_NAME     = 'sm.notification.sms';
    const NOTIFICATION_TYPE = 'sms';
    /**
     * Initialize resource model
     */
    public function _construct()
    {
        $this->_init(\SM\Notification\Model\ResourceModel\Sms::class);
    }
}
