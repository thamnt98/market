<?php
/**
 * Class ReorderRepositoryInterface
 * @package SM\DigitalProduct\Api
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Api;

use Magento\Framework\Exception\InputException;

interface ReorderRepositoryInterface
{
    const SUCCESS = 00 ;
    const TIMEOUT_RESPONSE_CODE = 23 ;
    const PROVIDER_CUT_OFF = 24 ;
    const ALREADY_PAID = 50 ;
    const ERROR = 100 ;

    /**
     * @param int $customerId
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem
     * @param null $quote
     * @return bool
     * @throws \Exception
     */
    public function reOrder($customerId, $cartId, \Magento\Quote\Api\Data\CartItemInterface $cartItem);
}
