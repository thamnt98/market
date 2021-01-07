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
}
