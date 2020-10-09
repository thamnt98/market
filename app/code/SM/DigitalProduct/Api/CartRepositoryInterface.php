<?php
/**
 * Interface CartRepositoryInterface
 * @package SM\DigitalProduct\
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\DigitalProduct\Api;

interface CartRepositoryInterface
{
    /**
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem
     * @return bool
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function addToCart($cartId, \Magento\Quote\Api\Data\CartItemInterface $cartItem);
}
