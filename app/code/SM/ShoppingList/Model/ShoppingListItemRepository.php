<?php

namespace SM\ShoppingList\Model;

use Exception;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Exception as WebapiException;
use Magento\Wishlist\Model\Item;
use Magento\Wishlist\Model\ItemFactory;
use Magento\Wishlist\Model\Wishlist as ShoppingList;
use Magento\Wishlist\Model\WishlistFactory as ShoppingListFactory;
use SM\ShoppingList\Api\Data\ResultDataInterface;
use SM\ShoppingList\Api\Data\ResultDataInterfaceFactory;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;
use SM\ShoppingList\Api\Data\ShoppingListDataInterfaceFactory;
use SM\ShoppingList\Api\ShoppingListItemRepositoryInterface;
use SM\ShoppingList\Helper\Converter;
use SM\ShoppingList\Helper\Data;
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
     * @var ShoppingListCollectionFactory
     */
    protected $shoppingListCollectionFactory;

    /**
     * @var HistoryCollectionFactory
     */
    protected $historyCollectionFactory;

    /**
     * @var ResultDataInterfaceFactory
     */
    protected $resultDataFactory;

    /**
     * @var Data
     */
    protected $shoppingListHelper;

    /**
     * @var Converter
     */
    protected $converter;

    /**
     * @var ShoppingListFactory
     */
    protected $wishlistFactory;

    /**
     * ShoppingListItemRepository constructor.
     * @param DataObjectHelper $dataObjectHelper
     * @param ShoppingListDataInterfaceFactory $shoppingListDataFactory
     * @param ProductRepository $productRepository
     * @param ItemFactory $itemFactory
     * @param ShoppingListCollectionFactory $shoppingListCollectionFactory
     * @param HistoryCollectionFactory $historyCollectionFactory
     * @param ResultDataInterfaceFactory $resultDataFactory
     * @param Data $shoppingListHelper
     * @param Converter $converter
     * @param ShoppingListFactory $wishlistFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        ShoppingListDataInterfaceFactory $shoppingListDataFactory,
        ProductRepository $productRepository,
        ItemFactory $itemFactory,
        ShoppingListCollectionFactory $shoppingListCollectionFactory,
        HistoryCollectionFactory $historyCollectionFactory,
        ResultDataInterfaceFactory $resultDataFactory,
        Data $shoppingListHelper,
        Converter $converter,
        ShoppingListFactory $wishlistFactory
    ) {
        $this->wishlistFactory = $wishlistFactory;
        $this->converter = $converter;
        $this->shoppingListHelper = $shoppingListHelper;
        $this->resultDataFactory = $resultDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->shoppingListDataFactory = $shoppingListDataFactory;
        $this->productRepository = $productRepository;
        $this->itemFactory = $itemFactory;
        $this->shoppingListCollectionFactory = $shoppingListCollectionFactory;
        $this->historyCollectionFactory = $historyCollectionFactory;
    }

    /**
     * @param int $itemId
     * @return bool
     * @throws WebapiException
     */
    public function deleteById($itemId)
    {
        $item = $this->validateShoppingListItem($itemId);
        return $this->delete($item);
    }

    /**
     * @param Item $item
     * @return bool
     * @throws WebapiException
     */
    public function delete($item)
    {
        $wishlist = $this->wishlistFactory->create()->load($item->getWishlistId());

        try {
            $item->delete();
            $wishlist->save();
            return true;
        } catch (Exception $exception) {
            throw new WebapiException(
                __($exception->getMessage()),
                0,
                WebapiException::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @param int $itemId
     * @return int|Item
     * @throws WebapiException
     */
    public function validateShoppingListItem($itemId)
    {
        /** @var Item $itemModel */
        $itemModel = $this->itemFactory->create()->load($itemId);
        if (!$itemModel->getId()) {
            throw new WebapiException(
                __("Item ID %1 is not exist", $itemId),
                0,
                WebapiException::HTTP_BAD_REQUEST
            );
        }
        return $itemModel;
    }

    /**
     * @param int $itemId
     * @param int[] $shoppingListIds
     * @return ResultDataInterface
     * @throws WebapiException
     */
    public function move($itemId, $shoppingListIds)
    {
        $itemModel = $this->validateShoppingListItem($itemId);
        $result = $this->add($shoppingListIds, $itemModel->getProductId());
        if ($result->getStatus() == 1) { // Check if move success
            try {
                $this->deleteById($itemId);
            } catch (Exception $exception) {
                throw new WebapiException(
                    __($exception->getMessage()),
                    0,
                    WebapiException::HTTP_BAD_REQUEST
                );
            }
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
     * @param int $productId
     * @return bool
     */
    protected function validateProduct($productId)
    {
        try {
            $this->productRepository->getById($productId);
            return true;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * @param int[] $shoppingListIds
     * @param int $productId
     * @return ResultDataInterface
     */
    public function add($shoppingListIds, $productId)
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
            $exist = "";
            if ($shoppingListCollection->count()) {
                /** @var ShoppingList $shoppingList */
                foreach ($shoppingListCollection as $shoppingList) {
                    $exist .= (($shoppingList->getData("name") == null) ?
                            __("My Favorites") : $shoppingList->getName()) . ", ";
                }
                $resultData->setStatus(0);
                $resultData->setMessage(__("This product is already in %1", trim($exist, ", ")));
            } else {
                /** @var ShoppingListCollection $shoppingListCollection */
                $shoppingListCollection = $this->shoppingListCollectionFactory->create();
                $shoppingListCollection->addFieldToFilter("main_table.wishlist_id", ["in" => $shoppingListIds]);
                $result = [];

                /** @var ShoppingList $shoppingList */
                foreach ($shoppingListCollection as $shoppingList) {
                    try {
                        $product = $this->productRepository->getById($productId);
                        $item = $shoppingList->addNewItem($product);
                        if ($item->getId()) {
                            $shoppingListData = $this->shoppingListDataFactory->create();
                            $this->dataObjectHelper->populateWithArray(
                                $shoppingListData,
                                $shoppingList->getData(),
                                ShoppingListDataInterface::class
                            );
                            if (($shoppingList->getData("name") == null)) {
                                $shoppingListData->setName(__("My Favorites"));
                            }
                            $result[] = $shoppingListData;
                        }
                    } catch (Exception $e) {
                        $resultData->setStatus(0)->setMessage($e->getMessage());
                        return $resultData;
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
