<?php
/**
 * SM\ShoppingList\Helper
 *
 * @copyright Copyright © 2021 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\ShoppingList\Helper;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\App\Emulation;
use Magento\Wishlist\Model\Item;
use Magento\Wishlist\Model\Wishlist;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;
use SM\ShoppingList\Api\Data\ShoppingListDataInterfaceFactory;
use SM\ShoppingList\Api\Data\ShoppingListItemDataInterface;
use SM\ShoppingList\Api\Data\ShoppingListItemDataInterfaceFactory;
use SM\ShoppingList\Model\ResourceModel\Item\Collection as ItemCollection;
use SM\ShoppingList\Model\ResourceModel\Item\CollectionFactory as ItemCollectionFactory;
use Magento\Wishlist\Model\ResourceModel\Wishlist\Collection as ListCollection;

/**
 * Class Converter
 * @package SM\ShoppingList\Helper
 */
class Converter extends AbstractHelper
{
    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var Emulation
     */
    protected $appEmulation;

    /**
     * @var ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * @var \SM\MobileApi\Helper\Product
     */
    protected $productHelper;

    /**
     * @var ShoppingListDataInterfaceFactory
     */
    protected $listDataFactory;

    /**
     * @var ShoppingListItemDataInterfaceFactory
     */
    protected $itemDataFactory;

    /**
     * @var ItemCollectionFactory
     */
    protected $itemCollectionFactory;

    /**
     * @var Data
     */
    protected $shoppingListHelper;

    /**
     * Converter constructor.
     * @param Context $context
     * @param Image $imageHelper
     * @param Emulation $appEmulation
     * @param ReviewFactory $reviewFactory
     * @param ProductRepository $productRepository
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \SM\MobileApi\Helper\Product $productHelper
     * @param ShoppingListDataInterfaceFactory $listDataFactory
     * @param ShoppingListItemDataInterfaceFactory $itemDataFactory
     * @param ItemCollectionFactory $itemCollectionFactory
     * @param Data $shoppingListHelper
     */
    public function __construct(
        Context $context,
        Image $imageHelper,
        Emulation $appEmulation,
        ReviewFactory $reviewFactory,
        ProductRepository $productRepository,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \SM\MobileApi\Helper\Product $productHelper,
        ShoppingListDataInterfaceFactory $listDataFactory,
        ShoppingListItemDataInterfaceFactory $itemDataFactory,
        ItemCollectionFactory $itemCollectionFactory,
        Data $shoppingListHelper
    ) {
        $this->shoppingListHelper = $shoppingListHelper;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->listDataFactory = $listDataFactory;
        $this->itemDataFactory = $itemDataFactory;
        $this->imageHelper = $imageHelper;
        $this->appEmulation = $appEmulation;
        $this->reviewFactory = $reviewFactory;
        $this->productRepository = $productRepository;
        $this->priceHelper = $priceHelper;
        $this->productHelper = $productHelper;
        parent::__construct($context);
    }


    /**
     * @param ShoppingListItemDataInterface $itemData
     * @return ShoppingListItemDataInterface
     * @throws \Exception
     */
    public function getProductInfo($itemData)
    {
        $this->appEmulation->startEnvironmentEmulation(
            $itemData->getStoreId(),
            Area::AREA_FRONTEND,
            true
        );
        /** @var Product $product */
        $product = $this->productRepository->getById($itemData->getProductId());
        $this->reviewFactory->create()->getEntitySummary($product, $itemData->getStoreId());
        $ratingSummary = $product->getRatingSummary()->getRatingSummary();
        $reviewCount = $product->getRatingSummary()->getReviewsCount();
        $itemData
            ->setProductId($product->getId())
            ->setCustomAttribute("product_url", $product->getProductUrl())
            ->setCustomAttribute("product_name", $product->getName())
            ->setCustomAttribute("product_rating", is_null($ratingSummary) ? 0 : $ratingSummary)
            ->setCustomAttribute("review_count", is_null($reviewCount) ? 0 : $reviewCount)
            ->setCustomAttribute(
                "product_image",
                $this->imageHelper->init($product, "product_base_image")->getUrl()
            );
        $itemData = $this->priceProcess($itemData, $product);
        $itemData->setProduct($this->productHelper->getProductListToResponseV2($product));
        $this->appEmulation->stopEnvironmentEmulation();

        return $itemData;
    }

    /**
     * @param ShoppingListItemDataInterface $itemData
     * @param Product $product
     * @return ShoppingListItemDataInterface
     */
    public function priceProcess($itemData, $product)
    {
        $itemData->setCustomAttribute("product_type", $product->getTypeId());
        if ($product->getTypeId() == Type::TYPE_BUNDLE) {
            $bundlePrice = $product->getPriceInfo()->getPrice('final_price');
            $itemData->setCustomAttribute(
                "product_price",
                $this->currencyFormat($bundlePrice->getMinimalPrice()->getValue())
            );
            return $itemData;
        } else {
            $price = $product->getPriceInfo()->getPrice('final_price');
            $itemData->setCustomAttribute(
                "product_price",
                $this->currencyFormat($price->getValue())
            );
            return $itemData;
        }
    }

    /**
     * @param float $value
     * @return float|string
     */
    public function currencyFormat($value)
    {
        return $this->priceHelper->currency($value, true, false);
    }

    /**
     * Use For API Get My Favorites and Shopping List Detail
     *
     * @param Wishlist $model
     * @return ShoppingListDataInterface
     */
    public function convertModelToResponse($model)
    {
        /** @var ShoppingListDataInterface $listData */
        $listData = $this->listDataFactory->create();
        $listData
            ->setWishlistId($model->getId())
            ->setName($model->getName())
            ->setSharingCode($model->getSharingCode());

        if (is_null($model->getData("name"))) {
            $listData->setName($this->shoppingListHelper->getDefaultShoppingListName());
        }

        $items = [];
        /** @var Item $item */
        foreach ($model->getItemCollection() as $item) {
            /** @var ShoppingListItemDataInterface $itemData */
            $itemData = $this->itemDataFactory->create();
            $itemData
                ->setWishlistItemId($item->getId())
                ->setProduct($this->productHelper->getProductListToResponseV2($item->getProduct()));

            array_push($items, $itemData);
        }

        $listData->setItems($items);

        return $listData;
    }

    /**
     * Use For API Get List Shopping List (My List Screen)
     *
     * @param ListCollection $collection
     * @return ShoppingListDataInterface[]
     */
    public function convertCollectionToResponse($collection)
    {
        $result = [];
        /** @var Wishlist $model */
        foreach ($collection as $model) {
            /** @var ShoppingListDataInterface $listData */
            $listData = $this->listDataFactory->create();
            $listData
                ->setWishlistId($model->getId())
                ->setName($model->getName())
                ->setLeft(0)
                ->setSharingCode($model->getSharingCode());

            $items = [];
            $count = 0;
            /** @var Item $item */
            foreach ($model->getItemCollection() as $item) {
                if ($count >= 4) {
                    $listData->setLeft($model->getItemCollection()->getSize() - 4);
                    break;
                }
                /** @var ShoppingListItemDataInterface $itemData */
                $itemData = $this->itemDataFactory->create();
                $itemData->setImage($this->productHelper->getMainImage($item->getProduct()));

                array_push($items, $itemData);
                $count++;
            }

            $listData->setItems($items);

            array_push($result, $listData);
        }

        return $result;
    }

    /**
     * Use API Get All Shopping List
     * Results will be shown in PDP screen when adding a new item to Shopping List
     *
     * @param int $productId
     * @param ListCollection $collection
     * @return ShoppingListDataInterface[]
     */
    public function convertCollectionToMinimalResponse($collection, $productId)
    {
        $result = [];
        $itemCollection = $this->itemCollectionFactory->create()
            ->addFieldToFilter("product_id", ["eq" => $productId]);

        $listIds = array_map(function ($item) {
            /** @var Item $item */
            return $item->getWishlistId();
        }, $itemCollection->getItems());

        foreach ($collection as $model) {
            /** @var ShoppingListDataInterface $listData */
            $listData = $this->listDataFactory->create();
            $listData
                ->setWishlistId($model->getId())
                ->setName($model->getName())
                ->setIsExist(in_array($model->getId(), $listIds));

            if (is_null($model->getData("name"))) {
                $listData->setName($this->shoppingListHelper->getDefaultShoppingListName());
            }

            array_push($result, $listData);
        }

        return $result;
    }
}
