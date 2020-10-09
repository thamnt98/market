<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Model\Data;

use Magento\Catalog\Api\Data\ProductInterface;
use SM\MobileApi\Api\Data\Product\ListItemInterface;

/**
 * Class RelatedProducts
 * @package SM\InspireMe\Model\Data
 */
class RelatedProducts extends \SM\MobileApi\Model\Data\Product\ListItem implements \SM\InspireMe\Api\Data\RelatedProductsInterface
{
    /**
     * @inheritDoc
     */
    public function getProductValue()
    {
        return $this->getData(self::PRODUCT_VALUE);
    }
}
