<?php
/**
 * SM\ShoppingList\ViewModel
 *
 * @copyright Copyright © 2021 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\ShoppingList\ViewModel;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Product;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\MultipleWishlist\Block\Customer\Wishlist\Management;
use Psr\Log\LoggerInterface;
use SM\Catalog\Block\Product\ProductList\Item\AddTo\Iteminfo;
use SM\Catalog\Helper\Data;
use SM\Label\Model\LabelViewer;
use Amasty\Label\Model\AbstractLabels;
use Magento\Catalog\Helper\Image;

/**
 * Class ItemPriceViewModel
 * @package SM\ShoppingList\ViewModel
 */
class ItemViewModel implements ArgumentInterface
{
    /**
     * @var ListProduct
     */
    protected $listProduct;

    /**
     * @var Iteminfo
     */
    protected $itemInfo;

    /**
     * @var Data
     */
    protected $catalogHelper;

    /**
     * @var LabelViewer
     */
    protected $labelViewer;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\MultipleWishlist\Helper\Data
     */
    protected $wishlistData;

    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var Management
     */
    protected $management;

    /**
     * ItemPriceViewModel constructor.
     * @param ListProduct $listProduct
     * @param Iteminfo $itemInfo
     * @param Data $catalogHelper
     * @param LabelViewer $labelViewer
     * @param LoggerInterface $logger
     * @param \Magento\MultipleWishlist\Helper\Data $wishlistData
     * @param CurrentCustomer $currentCustomer
     * @param Image $imageHelper
     * @param Management $management
     */
    public function __construct(
        ListProduct $listProduct,
        Iteminfo $itemInfo,
        Data $catalogHelper,
        LabelViewer $labelViewer,
        LoggerInterface $logger,
        \Magento\MultipleWishlist\Helper\Data $wishlistData,
        CurrentCustomer $currentCustomer,
        Image $imageHelper,
        Management $management
    ) {
        $this->management = $management;
        $this->imageHelper = $imageHelper;
        $this->wishlistData = $wishlistData;
        $this->currentCustomer = $currentCustomer;
        $this->logger = $logger;
        $this->labelViewer = $labelViewer;
        $this->catalogHelper = $catalogHelper;
        $this->itemInfo = $itemInfo;
        $this->listProduct = $listProduct;
    }

    /**
     * @param Product $product
     * @return string
     */
    public function getProductPrice($product)
    {
        return $this->listProduct->getProductPrice($product);
    }

    /**
     * @param Product $product
     * @return float|null
     */
    public function getDiscountPercent($product)
    {
        if ($product != null) {
            return $this->itemInfo->getDiscountPercent($product);
        }
        return null;
    }

    /**
     * @param Product $product
     * @return int
     */
    public function countProductChildren($product)
    {
        return $this->catalogHelper->countChildren($product);
    }

    /**
     * @param $product
     * @return string
     */
    public function getLabel($product)
    {
        $result = '';
        if ($product->getId()) {
            try {
                $result = $this->labelViewer->renderProductLabel(
                    $product,
                    AbstractLabels::CATEGORY_MODE,
                    false
                );
            } catch (\Exception $exception) {
                $this->logger->critical($exception->getMessage());
            }
        }

        return $result;
    }

    /**
     * @return \Magento\Framework\DataObject[]
     */
    public function getShoppingLists()
    {
        $customerId = $this->currentCustomer->getCustomerId();
        return $this->wishlistData->getCustomerWishlists($customerId)->getItems();
    }

    /**
     * @param Product $product
     * @return string
     */
    public function getProductImage($product)
    {
        return $this->imageHelper->init($product, 'product_base_image')->getUrl();
    }

    /**
     * @return bool
     */
    public function isDefaultWishlist()
    {
        $wishlist = $this->management->getCurrentWishlist();
        $defaultWishlistId = $this->management->getDefaultWishlist()->getId();
        return $wishlist->getId() == $defaultWishlistId;
    }
}
