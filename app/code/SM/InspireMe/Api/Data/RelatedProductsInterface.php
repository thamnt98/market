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
 * Interface RelatedProducts
 * @package SM\InspireMe\Api\Data
 */
interface RelatedProductsInterface extends \SM\MobileApi\Api\Data\Product\ListItemInterface
{
    const PRODUCT_VALUE = 'product_value';

    /**
     * @return int
     */
    public function getProductValue();
}
