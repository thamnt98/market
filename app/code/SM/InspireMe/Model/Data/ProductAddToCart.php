<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Model\Data;

use Magento\Framework\Model\AbstractModel;
use SM\InspireMe\Api\Data\ProductAddToCartInterface;

/**
 * Class ProductAddToCart
 * @package SM\InspireMe\Model\Data
 */
class ProductAddToCart extends AbstractModel implements \SM\InspireMe\Api\Data\ProductAddToCartInterface
{
    /**
     * @inheritDoc
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setProductId($value)
    {
        return $this->setData(self::PRODUCT_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getProductQty()
    {
        return $this->getData(self::PRODUCT_QTY);
    }

    /**
     * @inheritDoc
     */
    public function setProductQty($value)
    {
        return $this->setData(self::PRODUCT_QTY, $value);
    }
}
