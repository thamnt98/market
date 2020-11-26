<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: November, 26 2020
 * Time: 11:05 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model;

class EventSetting
{
    /**
     * @var \SM\Notification\Helper\CustomerSetting
     */
    protected $setting;

    /**
     * @var bool
     */
    protected $isPush = false;

    /**
     * @var bool
     */
    protected $isSms = false;

    /**
     * @var bool
     */
    protected $isEmail = false;

    /**
     * EventSetting constructor.
     *
     * @param \SM\Notification\Helper\CustomerSetting $setting
     */
    public function __construct(
        \SM\Notification\Helper\CustomerSetting $setting
    ) {
        $this->setting = $setting;
    }

    public function init($customerId, $event)
    {
        $setting = $this->setting->getCustomerSetting($customerId);

        $this->isEmail = in_array(
            $this->setting->generateSettingCode($event, 'email'),
            $setting
        );
        $this->isPush = in_array(
            $this->setting->generateSettingCode($event, 'push'),
            $setting
        );
        $this->isSms = in_array(
            $this->setting->generateSettingCode($event, 'sms'),
            $setting
        );

        return $this;
    }

    /**
     * @return bool
     */
    public function isPush()
    : bool
    {
        return $this->isPush;
    }

    /**
     * @return bool
     */
    public function isSms()
    : bool
    {
        return $this->isSms;
    }

    /**
     * @return bool
     */
    public function isEmail()
    : bool
    {
        return $this->isEmail;
    }
}
