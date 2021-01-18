<?php
/**
 * Class IsPaymentMethod
 * @package SM\Sales\Model\Order
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Sales\Model\Order;

use \Trans\Mepay\Gateway\Request\PaymentSourceMethodDataBuilder as PaymentSource;

class IsPaymentMethod
{
    const CARD_METHODS = [
        "sprint_allbankfull_cc",
        'trans_mepay_cc',
        'trans_mepay_debit',
        'trans_mepay_qris'
    ];

    const VIRTUAL_METHODS = [
        "sprint_bca_va",
        'sprint_mega_cc',
        'trans_mepay_va'
    ];

    const PREAUTH_METHODS = [
        'trans_mepay_cc'
    ];

    /**
     * @param string $method
     * @return bool
     */
    public static function isVirtualAccount(string $method): bool
    {
        return in_array($method, self::VIRTUAL_METHODS);
    }

    /**
     * @param string $method
     * @return bool
     */
    public static function isCard(string $method): bool
    {
        return in_array($method, self::CARD_METHODS);
    }

        /**
     * @param string $method
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    public static function isPreAuth(string $method, \Magento\Sales\Model\Order $order): bool
    {
        if (in_array($method, self::PREAUTH_METHODS)) {
            $ccType = $order->getPayment()->getCcType();
            if ($ccType == PaymentSource::AUTH_CAPTURE) {
                return true;
            }
        }
        return false;
    }
}
