<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Api\Data;

/**
 * Interface ProductAddToCartInterface
 * @package SM\InspireMe\Api\Data
 */
interface ProductAddToCartInterface
{
    const PRODUCT_ID  = 'product_id';
    const PRODUCT_QTY = 'product_qty';

    /**
     * @return int
     */
    public function getProductId();

    /**
     * @param int $value
     * @return $this
     */
    public function setProductId($value);

    /**
     * @return int
     */
    public function getProductQty();

    /**
     * @param int $value
     * @return $this
     */
    public function setProductQty($value);
}
