<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 10 2020
 * Time: 3:01 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model;

class Email extends \Magento\Framework\Model\AbstractModel
{
    const CONSUMER_NAME     = 'sm.notification.email';
    const NOTIFICATION_TYPE = 'email';

    /**
     * Initialize resource model
     */
    public function _construct()
    {
        $this->_init(\SM\Notification\Model\ResourceModel\Email::class);
    }
}
