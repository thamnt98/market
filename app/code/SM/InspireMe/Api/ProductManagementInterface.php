<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Api;

/**
 * Class ProductManagementInterface
 * @package SM\InspireMe\Api
 */
interface ProductManagementInterface
{
    /**
     * @param int $cartId
     * @param \SM\InspireMe\Api\Data\ProductAddToCartInterface[] $products
     * @param string $formKey
     * @return boolean
     */
    public function addSelectedToCart($cartId, $products, $formKey = null);
}
