<?php
/**
 * Class CartTotalRepository
 * @package SM\Checkout\Model\Cart
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */


namespace SM\Checkout\Model\Cart;

use SM\Checkout\Api\CartTotalRepositoryInterface;

class CartTotalRepository extends \Magento\Quote\Model\Cart\CartTotalRepository implements CartTotalRepositoryInterface
{
}
