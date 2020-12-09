<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: November, 26 2020
 * Time: 11:36 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_DEFAULT_IMAGE                      = 'sm_notification/image/default';
    const XML_IMAGE_ORDER_STATUS_COMPLETED       = 'sm_notification/image/order_status/completed';
    const XML_IMAGE_ORDER_STATUS_READY_TO_PICKUP = 'sm_notification/image/order_status/ready_to_pickup';
    const XML_IMAGE_ORDER_STATUS_DELIVERED       = 'sm_notification/image/order_status/delivered';
    const XML_IMAGE_ORDER_STATUS_IN_DELIVERY     = 'sm_notification/image/order_status/in_delivery';
    const XML_IMAGE_PAYMENT_FAILED               = 'sm_notification/image/payment/failed';
    const XML_IMAGE_PAYMENT_SUCCESS              = 'sm_notification/image/payment/success';

    const XML_ABANDONED_CART_HOUR             = 'sm_notification/generate/abandoned_cart_time';
    const XML_ABANDONED_CART_REPEAT_DAY       = 'sm_notification/generate/abandoned_cart_repeat_time';
    const XML_REMIND_PICKUP_DAY               = 'sm_notification/generate/remind_pickup';
    const XML_REMIND_PICKUP_EXPIRING_SOON_DAY = 'sm_notification/generate/remind_pickup_expired';
    const XML_PICKUP_LIMIT_DAY                = 'carriers/store_pickup/date_limit';
    const XML_PAYMENT_EXPIRING_MINUTE         = 'sm_notification/generate/payment_expiring_soon_time';
    const XML_VA_PAYMENT_LIST                 = 'sm_notification/generate/va_payment';
    const XML_POLICE_HELP_PAGE                = 'sm_notification/generate/policy_help_id';
    const XML_TERM_CONDITION_HELP_PAGE        = 'sm_notification/generate/term_help_id';

    const XML_EVENT_TYPE = 'sm_notification/event_type_config/event_type';

    /**
     * @param int|string|null $store
     *
     * @return array
     */
    public function getVaPaymentList($store = null)
    {
        $config = $this->scopeConfig->getValue(
            self::XML_VA_PAYMENT_LIST,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        if ($config) {
            return explode(',', $config);
        } else {
            return [];
        }
    }

    /**
     * @param int|string|null $store
     *
     * @return int
     */
    public function getPickupLimitDay($store = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PICKUP_LIMIT_DAY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|string|null $store
     *
     * @return int
     */
    public function getVaPaymentExpiringMinute($store = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PAYMENT_EXPIRING_MINUTE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|string|null $store
     *
     * @return int
     */
    public function getRemindPickupExpiringSoonDay($store = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_REMIND_PICKUP_EXPIRING_SOON_DAY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|string|null $store
     *
     * @return int
     */
    public function getRemindPickupDay($store = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_REMIND_PICKUP_DAY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|string|null $store
     *
     * @return int
     */
    public function getAbandonedCartHour($store = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_ABANDONED_CART_HOUR,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|string|null $store
     *
     * @return int
     */
    public function getAbandonedCartRepeatDay($store = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_ABANDONED_CART_REPEAT_DAY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|string|null $store
     *
     * @return string
     */
    public function getDefaultImage($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_DEFAULT_IMAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|string|null $store
     *
     * @return string
     */
    public function getOrderCompleteImage($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_IMAGE_ORDER_STATUS_COMPLETED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|string|null $store
     *
     * @return string
     */
    public function getOrderPickupImage($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_IMAGE_ORDER_STATUS_READY_TO_PICKUP,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|string|null $store
     *
     * @return string
     */
    public function getOrderDeliveredImage($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_IMAGE_ORDER_STATUS_DELIVERED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|string|null $store
     *
     * @return string
     */
    public function getOrderInDeliverImage($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_IMAGE_ORDER_STATUS_IN_DELIVERY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getPaymentFailedImage($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_IMAGE_PAYMENT_FAILED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|string|null $store
     *
     * @return string
     */
    public function getPaymentSuccessImage($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_IMAGE_PAYMENT_SUCCESS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|string|null $store
     *
     * @return string
     */
    public function getOrderPendingStatus($store = null)
    {
        return $this->scopeConfig->getValue(
            \Trans\Sprint\Helper\Config::GENERAL_NEW_ORDER_STATUS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|string|null $store
     *
     * @return string
     */
    public function getPolicyHelpId($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_POLICE_HELP_PAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|string|null $store
     *
     * @return string
     */
    public function getTermHelpId($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_TERM_CONDITION_HELP_PAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
