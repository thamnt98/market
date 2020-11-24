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

namespace SM\Notification\Model;

/**
 * Class to generate API configuration
 *
 * @method Notification setEvent($event)
 * @method string getEvent()
 * @method Notification setSubEvent($event)
 * @method string getSubEvent()
 * @method Notification setIsSystem($event)
 * @method bool getIsSystem()
 * @method Notification setRedirectType($type)
 * @method string getRedirectType()
 * @method Notification setRedirectId($id)
 * @method string|int getRedirectId()
 * @method Notification setImage($image)
 * @method string getImage()
 * @method Notification setTitle($title)
 * @method string getTitle()
 * @method Notification setContent($content)
 * @method string getContent()
 * @method Notification setSms($content)
 * @method string getSms()
 * @method Notification setPushContent($content)
 * @method string getPushContent()
 * @method Notification setPushTitle($title)
 * @method string getPushTitle()
 * @method Notification setEmailSubject($title)
 * @method string getEmailSubject()
 * @method Notification setEmailTemplate($content)
 * @method string getEmailTemplate()
 * @method Notification setStartDate($date)
 * @method string getStartDate()
 * @method Notification setEndDate($date)
 * @method string getEndDate()
 * @method Notification setCreatedAt($date)
 * @method string getCreatedAt()
 * @method Notification setParams($params)
 * @method int[]|string getParams()
 * @method Notification setEmailParams($params)
 * @method int[]|string getEmailParams()
 * @method Notification setCustomerIds($ids)
 * @method int[]|string getCustomerIds()
 */
class Notification extends \Magento\Framework\Model\AbstractModel
{
    const SYNC_PENDING = 0;
    const SYNCED       = 1; // sent to queue
    const SENT         = 2; // sent to customer

    const EVENT_ORDER_STATUS    = 'order_status';
    const EVENT_UPDATE          = 'update';
    const EVENT_SERVICE         = 'service';
    const EVENT_PROMO_AND_EVENT = 'promo_event';
    const EVENT_INFO            = 'information';
    const EVENT_REORDER         = 'reorder';
    const EVENT_MY_APPOINTMENT  = 'my_appointment';
    const EVENT_SUBSCRIPTION    = 'subscription';
    const EVENT_CHAT_RECAP      = 'chat_recap';
    const EVENT_UNKNOWN_DEVICE  = 'unknown_device';

    const EVENT_ORDER_STATUS_SIGN_OUT  = 'order_status_sign_out';

    protected $_eventPrefix = 'sm_notification';
    protected $_eventObject = 'message';

    /**
     * Initialize resource model
     */
    public function _construct()
    {
        $this->_init(\SM\Notification\Model\ResourceModel\Notification::class);
    }
}
