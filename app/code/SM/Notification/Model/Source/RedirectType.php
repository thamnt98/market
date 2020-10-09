<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 09 2020
 * Time: 11:12 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model\Source;

class RedirectType implements \Magento\Framework\Data\OptionSourceInterface
{
    const TYPE_HOME              = 'home';
    const TYPE_CART              = 'cart';
    const TYPE_SHOPPING_LIST     = 'shopping_list';
    const TYPE_ORDER_DETAIL      = 'order';
    const TYPE_VOUCHER_DETAIL    = 'voucher';
    const TYPE_VOUCHER_LIST      = 'voucher_list';
    const TYPE_BRAND             = 'brand';
    const TYPE_TERM              = 'term';
    const TYPE_SUBSCRIPTION_LIST = 'subs';
    const TYPE_GIFT_LIST         = 'gift';
    const TYPE_PDP               = 'product';
    const TYPE_CAMPAIGN          = 'campaign';

    public function toOptionArray()
    {
        return [
            [
                'label' => __('Home Page'),
                'value' => self::TYPE_HOME,
            ],
            [
                'label' => __('Cart'),
                'value' => self::TYPE_CART,
            ],
            [
                'label' => __('Shopping List'),
                'value' => self::TYPE_SHOPPING_LIST,
            ],
            [
                'label' => __('Order Detail'),
                'value' => self::TYPE_ORDER_DETAIL,
            ],
            [
                'label' => __('Voucher Detail'),
                'value' => self::TYPE_VOUCHER_DETAIL,
            ],
            [
                'label' => __('Voucher List'),
                'value' => self::TYPE_VOUCHER_LIST,
            ],
            [
                'label' => __('Brand Page'),
                'value' => self::TYPE_BRAND,
            ],
            [
                'label' => __('Campaign'),
                'value' => self::TYPE_CAMPAIGN,
            ],
            [
                'label' => __('Subscription List'),
                'value' => self::TYPE_SUBSCRIPTION_LIST,
            ],
            [
                'label' => __('Gift List Detail'),
                'value' => self::TYPE_GIFT_LIST,
            ],
            [
                'label' => __('T&C Detail'),
                'value' => self::TYPE_TERM,
            ],
            [
                'label' => __('Product Detail Page'),
                'value' => self::TYPE_PDP,
            ],
        ];
    }
}
