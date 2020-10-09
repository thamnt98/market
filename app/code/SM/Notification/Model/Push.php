<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 08 2020
 * Time: 5:43 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model;

class Push extends \Magento\Framework\Model\AbstractModel
{
    const CONSUMER_NAME     = 'sm.notification.push';
    const NOTIFICATION_TYPE = 'push';

    /**
     * Initialize resource model
     */
    public function _construct()
    {
        $this->_init(\SM\Notification\Model\ResourceModel\Push::class);
    }
}
