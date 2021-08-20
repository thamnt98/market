<?php
/**
 * SM\ShoppingList\ViewModel
 *
 * @copyright Copyright © 2021 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\ShoppingList\ViewModel;

use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class MyListViewModel
 * @package SM\ShoppingList\ViewModel
 */
class MyListViewModel implements ArgumentInterface
{
    /**
     * @var \SM\MobileApi\Model\Product\Image
     */
    protected $imageHelper;

    /**
     * MyListViewModel constructor.
     * @param \SM\MobileApi\Model\Product\Image $imageHelper
     */
    public function __construct(
        \SM\MobileApi\Model\Product\Image $imageHelper
    ) {
        $this->imageHelper = $imageHelper;
    }

    /**
     * @param Product $product
     * @return string
     */
    public function getImageUrl($product)
    {
        return $this->imageHelper->getMainImage($product);
    }
}
