<?php

namespace SM\ShoppingList\Model;

use Exception;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Stdlib\DateTime;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteFactory;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\App\Emulation;
use Magento\Wishlist\Model\Item;
use Magento\Wishlist\Model\ItemFactory;
use Magento\Wishlist\Model\Wishlist as ShoppingList;
use Magento\Wishlist\Model\WishlistFactory as ShoppingListFactory;
use SM\ShoppingList\Api\Data\ResultDataInterface;
use SM\ShoppingList\Api\Data\ResultDataInterfaceFactory;
use SM\ShoppingList\Api\Data\ShoppingListDataInterfaceFactory;
use SM\ShoppingList\Api\Data\ShoppingListItemDataInterface;
use SM\ShoppingList\Api\Data\ShoppingListItemDataInterfaceFactory;
use SM\ShoppingList\Api\Data\ShoppingListItemSearchResultsInterface;
use SM\ShoppingList\Api\Data\ShoppingListItemSearchResultsInterfaceFactory;
use SM\ShoppingList\Api\ShoppingListItemRepositoryInterface;
use SM\ShoppingList\Model\Data\ShoppingListItem;
use SM\ShoppingList\Model\ResourceModel\Item\Collection as ShoppingListItemCollection;
use SM\ShoppingList\Model\ResourceModel\Item\CollectionFactory as ItemCollectionFactory;
use SM\ShoppingList\Model\ResourceModel\ShareHistory\Collection as HistoryCollection;
use SM\ShoppingList\Model\ResourceModel\ShareHistory\CollectionFactory as HistoryCollectionFactory;
use SM\ShoppingList\Model\ResourceModel\Wishlist\Collection as ShoppingListCollection;
use SM\ShoppingList\Model\ResourceModel\Wishlist\CollectionFactory as ShoppingListCollectionFactory;

/**
 * Class ShoppingListItemRepository
 * @package SM\ShoppingList\Model
 */
class ShoppingListItemRepository implements ShoppingListItemRepositoryInterface
{
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var ShoppingListFactory
     */
    protected $shoppingListFactory;
    /**
     * @var ShoppingListDataInterfaceFactory
     */
    protected $shoppingListDataFactory;
    /**
     * @var ProductRepository
     */
    protected $productRepository;
    /**
     * @var ItemFactory
     */
    protected $itemFactory;
    /**
     * @var ShoppingListItemDataInterfaceFactory
     */
    protected $itemDataFactory;
    /**
     * @var ShoppingListCollectionFactory
     */
    protected $shoppingListCollectionFactory;
    /**
     * @var ShoppingListItemSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;
    /**
     * @var ItemCollectionFactory
     */
    protected $itemCollectionFactory;
    /**
     * @var HistoryCollectionFactory
     */
    protected $historyCollectionFactory;
    /**
     * @var ReviewFactory
     */
    protected $reviewFactory;
    /**
     * @var Data
     */
    protected $priceHelper;
    /**
     * @var ResultDataInterfaceFactory
     */
    protected $resultDataFactory;
    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;
    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote
     */
    protected $quoteModel;
    /**
     * @var Image
     */
    protected $imageHelper;
    /**
     * @var Emulation
     */
    protected $appEmulation;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * ShoppingListItemRepository constructor.
     * @param ShoppingListFactory $shoppingListFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ShoppingListDataInterfaceFactory $shoppingListDataFactory
     * @param ProductRepository $productRepository
     * @param ItemFactory $itemFactory
     * @param ShoppingListItemDataInterfaceFactory $itemDataFactory
     * @param ShoppingListCollectionFactory $shoppingListCollectionFactory
     * @param ShoppingListItemSearchResultsInterfaceFactory $searchCriteriaInterfaceFactory
     * @param ItemCollectionFactory $itemCollectionFactory
     * @param HistoryCollectionFactory $historyCollectionFactory
     * @param Image $imageHelper
     * @param ReviewFactory $reviewFactory
     * @param Data $priceHelper
     * @param QuoteFactory $quoteFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote $quoteModel
     * @param ResultDataInterfaceFactory $resultDataFactory
     * @param Emulation $appEmulation
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ShoppingListFactory $shoppingListFactory,
        DataObjectHelper $dataObjectHelper,
        ShoppingListDataInterfaceFactory $shoppingListDataFactory,
        ProductRepository $productRepository,
        ItemFactory $itemFactory,
        ShoppingListItemDataInterfaceFactory $itemDataFactory,
        ShoppingListCollectionFactory $shoppingListCollectionFactory,
        ShoppingListItemSearchResultsInterfaceFactory $searchCriteriaInterfaceFactory,
        ItemCollectionFactory $itemCollectionFactory,
        HistoryCollectionFactory $historyCollectionFactory,
        Image $imageHelper,
        ReviewFactory $reviewFactory,
        Data $priceHelper,
        QuoteFactory $quoteFactory,
        \Magento\Quote\Model\ResourceModel\Quote $quoteModel,
        ResultDataInterfaceFactory $resultDataFactory,
        Emulation $appEmulation,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->imageHelper = $imageHelper;
        $this->appEmulation = $appEmulation;
        $this->quoteFactory = $quoteFactory;
        $this->quoteModel = $quoteModel;
        $this->resultDataFactory = $resultDataFactory;
        $this->priceHelper = $priceHelper;
        $this->reviewFactory = $reviewFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->shoppingListFactory = $shoppingListFactory;
        $this->shoppingListDataFactory = $shoppingListDataFactory;
        $this->productRepository = $productRepository;
        $this->itemFactory = $itemFactory;
        $this->itemDataFactory = $itemDataFactory;
        $this->shoppingListCollectionFactory = $shoppingListCollectionFactory;
        $this->searchResultsFactory = $searchCriteriaInterfaceFactory;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->historyCollectionFactory = $historyCollectionFactory;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return ShoppingListItemSearchResultsInterface
     * @throws Exception
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var ShoppingListItemSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var ShoppingListItemCollection $itemCollection */
        $itemCollection = $this->itemCollectionFactory->create();
        $itemCollection->getSelectProductName()
            ->itemFilter($searchCriteria)
            ->itemSort($searchCriteria)
            ->setCurPage($searchCriteria->getCurrentPage())
            ->setPageSize($searchCriteria->getPageSize());
        $searchResults->setTotalCount($itemCollection->getSize());
        $items = $this->getListItems($itemCollection);
        $searchResults->setTotalCount($itemCollection->getSize());
        $searchResults->setItems($items);
        return $searchResults;
    }

    /**
     * @param ShoppingListItemCollection $itemCollection
     * @return array
     * @throws Exception
     */
    public function getListItems($itemCollection)
    {
        $items = [];
        foreach ($itemCollection->getData() as $item) {
            /** @var ShoppingListItemDataInterface $itemData */
            $itemData = $this->itemDataFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $itemData,
                $item,
                "SM\ShoppingList\Api\Data\ShoppingListItemDataInterface"
            );

            $items[] = $this->getProductInfo($itemData);
        }
        return $items;
    }

    /**
     * @param ShoppingListItemDataInterface $itemData
     * @return ShoppingListItemDataInterface
     * @throws Exception
     */
    protected function getProductInfo($itemData)
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
        $this->appEmulation->stopEnvironmentEmulation();

        return $itemData;
    }

    /**
     * @param ShoppingListItemDataInterface $itemData
     * @param Product $product
     * @return ShoppingListItemDataInterface
     */
    protected function priceProcess($itemData, $product)
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
    protected function currencyFormat($value)
    {
        return $this->priceHelper->currency($value, true, false);
    }

    /**
     * @param int $itemId
     * @return bool
     * @throws Exception
     */
    public function deleteById($itemId)
    {
        /** @var Item $item */
        $item = $this->validateShoppingListItem($itemId);
        if ($item instanceof Item) {
            /** @var ShoppingList $shoppingListModel */
            $shoppingListModel = $this->shoppingListFactory->create()->load($item->getWishlistId());
            $this->updateHistory($shoppingListModel->getSharingCode());
            $shoppingListModel->generateSharingCode()->save();
            $item->delete();
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int $itemId
     * @return ShoppingListItemDataInterface
     * @throws NoSuchEntityException
     */
    public function getById($itemId)
    {
        $itemModel = $this->itemFactory->create()->load($itemId);
        /** @var ShoppingListItemDataInterface $itemData */
        $itemData = $this->itemDataFactory->create();

        $this->dataObjectHelper->populateWithArray(
            $itemData,
            $itemModel->getData(),
            "SM\ShoppingList\Api\Data\ShoppingListItemDataInterface"
        );
        /** @var Product $product */
        $product = $this->productRepository->getById($itemData->getProductId());
        $itemData
            ->setCustomAttribute("product_name", $product->getName());
        return $itemData;
    }

    /**
     * @param int $itemId
     * @return int|Item
     */
    public function validateShoppingListItem($itemId)
    {
        /** @var Item $itemModel */
        $itemModel = $this->itemFactory->create()->load($itemId);
        if (!$itemModel->getId()) {
            return 0;
        }
        return $itemModel;
    }

    /**
     * @param ShoppingListItemDataInterface $item
     * @param int[] $shoppingListIds
     * @return ResultDataInterface
     */
    public function move(ShoppingListItemDataInterface $item, $shoppingListIds)
    {
        $result = $this->add($shoppingListIds, $item->getProductId(), $item->getStoreId());
        try {
            $this->deleteById($item->getWishlistItemId());
        } catch (Exception $exception) {
        }
        return $result;
    }

    /**
     * @param string $sharingCode
     */
    public function updateHistory($sharingCode)
    {
        /** @var HistoryCollection $historyCollection */
        $historyCollection = $this->historyCollectionFactory->create();
        $historyCollection->addFieldToFilter("sharing_code", ["eq" => $sharingCode]);
        $historyCollection->walk("delete");
    }

    /**
     * @param int $customerId
     * @param ShoppingListItemDataInterface $itemData
     * @return bool
     * @throws Exception
     */
    public function addToCart($customerId, $itemData)
    {
        /** @var Quote $quote */
        $quote = $this->quoteFactory->create();
        $this->quoteModel->loadByCustomerId($quote, $customerId);

        /** @var Product $product */
        $product = $this->productRepository->getById($itemData->getProductId());

        /** @var Quote\Item $item */
        $item = $quote->addProduct($product);

        if (!is_string($item)) {
            $quote->collectTotals()->save();
            $this->deleteById($itemData->getWishlistItemId());
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param ShoppingListItemDataInterface $item
     * @return ShoppingListItemDataInterface
     * @throws Exception
     */
    public function create(ShoppingListItemDataInterface $item)
    {
        $itemModel = $this->itemFactory->create()
            ->setProductId($item->getProductId())
            ->setWishlistId($item->getWishlistId())
            ->setAddedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT))
            ->setStoreId($item->getStoreId())
            ->setQty(1)
            ->save();

        /** @var ShoppingListItemDataInterface $itemData */
        $itemData = $this->itemDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $itemData,
            $itemModel->getData(),
            "SM\ShoppingList\Api\Data\ShoppingListItemDataInterface"
        );
        return $itemData;
    }

    /**
     * @param int $productId
     * @return bool
     */
    protected function validateProduct($productId)
    {
        try {
            $product = $this->productRepository->getById($productId);
            return true;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * @param int[] $shoppingListIds
     * @param int $productId
     * @param int $storeId
     * @return ResultDataInterface
     */
    public function add($shoppingListIds, $productId, $storeId)
    {
        /** @var ResultDataInterface $resultData */
        $resultData = $this->resultDataFactory->create();

        if ($this->validateProduct($productId)) {
            /** @var ShoppingListCollection $shoppingListCollection */
            $shoppingListCollection = $this->shoppingListCollectionFactory->create();
            $shoppingListCollection->getSelectItems();
            $shoppingListCollection->addFieldToFilter("main_table.wishlist_id", ["in" => $shoppingListIds]);
            $shoppingListCollection->addFieldToFilter("wishlist_item.product_id", ["eq" => $productId]);
            $shoppingListCollection->getSelect()->group("main_table.wishlist_id");
//            var_dump($shoppingListCollection->getSelect()->__toString());die;
            $exist = "";
            if ($shoppingListCollection->count()) {
                /** @var ShoppingList $shoppingList */
                foreach ($shoppingListCollection as $shoppingList) {
                    $exist .= (($shoppingList->getData("name") == null) ?
                            __("My Favorites") : $shoppingList->getName()) . ", ";
                }
                $resultData->setStatus(0);
                $resultData->setMessage(__("You have this product in %1 already.", trim($exist, ", ")));
            } else {
                /** @var ShoppingListCollection $shoppingListCollection */
                $shoppingListCollection = $this->shoppingListCollectionFactory->create();
                $shoppingListCollection->addFieldToFilter("main_table.wishlist_id", ["in" => $shoppingListIds]);
                $result = [];

                /** @var ShoppingList $shoppingList */
                foreach ($shoppingListCollection as $shoppingList) {
                    $this->updateHistory($shoppingList->getSharingCode());
                    $shoppingList->generateSharingCode();

                    /** @var ShoppingListItemDataInterface $itemData */
                    $itemData = $this->itemDataFactory->create()
                        ->setStoreId($storeId)
                        ->setProductId($productId)
                        ->setWishlistId($shoppingList->getId());
                    try {
                        if ($this->create($itemData) instanceof ShoppingListItem) {
                            $shoppingListData = $this->shoppingListDataFactory->create();
                            $this->dataObjectHelper->populateWithArray(
                                $shoppingListData,
                                $shoppingList->getData(),
                                "SM\ShoppingList\Api\Data\ShoppingListDataInterface"
                            );
                            if (($shoppingList->getData("name") == null)) {
                                $shoppingListData->setName(__("My Favorites"));
                            }
                            $result[] = $shoppingListData;
                        }
                    } catch (Exception $e) {
                        $resultData->setStatus(0)->setMessage(__("Unable to add item to shopping list"));
                    }
                }
                $resultData->setStatus(1)->setResult($result);
            }
        } else {
            $resultData->setStatus(0)->setMessage(__("Product not found"));
        }
        return $resultData;
    }
}
