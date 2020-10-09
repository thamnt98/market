<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: June, 11 2020
 * Time: 4:16 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model;

class TriggerEvent extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     */
    public function _construct()
    {
        $this->_init(\SM\Notification\Model\ResourceModel\TriggerEvent::class);
    }
}
