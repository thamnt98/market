<?php
/**
 * SM\ShoppingList\Model
 *
 * @copyright Copyright © 2021 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\ShoppingList\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Math\Random;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Model\Item;
use Magento\Wishlist\Model\ItemFactory;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory;
use Magento\Wishlist\Model\ResourceModel\Wishlist as ResourceWishlist;
use Magento\Wishlist\Model\ResourceModel\Wishlist\Collection;

/**
 * Class Wishlist
 * @package SM\ShoppingList\Model
 */
class Wishlist extends \Magento\Wishlist\Model\Wishlist
{
    /**
     * @var Json
     */
    protected $serializer;

    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Catalog\Helper\Product $catalogProduct,
        Data $wishlistData,
        ResourceWishlist $resource,
        Collection $resourceCollection,
        StoreManagerInterface $storeManager,
        DateTime\DateTime $date,
        ItemFactory $wishlistItemFactory,
        CollectionFactory $wishlistCollectionFactory,
        ProductFactory $productFactory,
        Random $mathRandom,
        DateTime $dateTime,
        ProductRepositoryInterface $productRepository,
        $useCurrentWebsite = true,
        array $data = [],
        Json $serializer = null,
        StockRegistryInterface $stockRegistry = null,
        ScopeConfigInterface $scopeConfig = null
    ) {
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
        parent::__construct(
            $context,
            $registry,
            $catalogProduct,
            $wishlistData,
            $resource,
            $resourceCollection,
            $storeManager,
            $date,
            $wishlistItemFactory,
            $wishlistCollectionFactory,
            $productFactory,
            $mathRandom,
            $dateTime,
            $productRepository,
            $useCurrentWebsite,
            $data,
            $serializer,
            $stockRegistry,
            $scopeConfig
        );
    }

    /**
     * Adds new product to wishlist.
     *
     * Returns new item or string on error.
     *
     * @param int|Product $product
     * @param DataObject|array|string|null $buyRequest
     * @param bool $forciblySetQty
     * @return Item|string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @throws LocalizedException
     * @throws \InvalidArgumentException
     */

    public function addNewItem($product, $buyRequest = null, $forciblySetQty = false)
    {
        /*
         * Always load product, to ensure:
         * a) we have new instance and do not interfere with other products in wishlist
         * b) product has full set of attributes
         */
        if ($product instanceof Product) {
            $productId = $product->getId();
            // Maybe force some store by wishlist internal properties
            $storeId = $product->hasWishlistStoreId() ? $product->getWishlistStoreId() : $product->getStoreId();
        } else {
            $productId = (int)$product;
            if (isset($buyRequest) && $buyRequest->getStoreId()) {
                $storeId = $buyRequest->getStoreId();
            } else {
                $storeId = $this->_storeManager->getStore()->getId();
            }
        }

        try {
            /** @var Product $product */
            $product = $this->productRepository->getById($productId, false, $storeId);
        } catch (NoSuchEntityException $e) {
            throw new LocalizedException(__('Cannot specify product.'));
        }

        if ($buyRequest instanceof DataObject) {
            $_buyRequest = $buyRequest;
        } elseif (is_string($buyRequest)) {
            $isInvalidItemConfiguration = false;
            try {
                $buyRequestData = $this->serializer->unserialize($buyRequest);
                if (!is_array($buyRequestData)) {
                    $isInvalidItemConfiguration = true;
                }
            } catch (\Exception $exception) {
                $isInvalidItemConfiguration = true;
            }
            if ($isInvalidItemConfiguration) {
                throw new \InvalidArgumentException('Invalid wishlist item configuration.');
            }
            $_buyRequest = new DataObject($buyRequestData);
        } elseif (is_array($buyRequest)) {
            $_buyRequest = new DataObject($buyRequest);
        } else {
            $_buyRequest = new DataObject();
        }

        /* @var $product Product */
        $cartCandidates = $product->getTypeInstance()->processConfiguration($_buyRequest, clone $product);

        /**
         * Error message
         */
        if (is_string($cartCandidates)) {
            return $cartCandidates;
        }

        /**
         * If prepare process return one object
         */
        if (!is_array($cartCandidates)) {
            $cartCandidates = [$cartCandidates];
        }

        $errors = [];
        $items = [];

        foreach ($cartCandidates as $candidate) {
            if ($candidate->getParentProductId()) {
                continue;
            }
            $candidate->setWishlistStoreId($storeId);

            $qty = $candidate->getQty() ? $candidate->getQty() : 1;
            // No null values as qty. Convert zero to 1.
            $item = $this->_addCatalogProduct($candidate, $qty, $forciblySetQty);
            $items[] = $item;

            // Collect errors instead of throwing first one
            if ($item->getHasError()) {
                $errors[] = $item->getMessage();
            }
        }

        $this->_eventManager->dispatch('wishlist_product_add_after', ['items' => $items]);

        return $item;
    }
}
