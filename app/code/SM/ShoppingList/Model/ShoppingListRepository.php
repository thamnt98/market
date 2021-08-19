<?php

namespace SM\ShoppingList\Model;

use Exception;
use Magento\Framework\Webapi\Exception as WebapiException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Wishlist\Model\Item;
use Magento\Wishlist\Model\ItemFactory;
use Magento\Wishlist\Model\ResourceModel\Item\Collection as ItemCollection;
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\WishlistFactory as ShoppingListFactory;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;
use SM\ShoppingList\Api\Data\ShoppingListDataInterfaceFactory;
use SM\ShoppingList\Api\ShoppingListRepositoryInterface;
use SM\ShoppingList\Helper\Converter;
use SM\ShoppingList\Model\ResourceModel\Item\CollectionFactory as ShoppingListItemCollectionFactory;
use SM\ShoppingList\Model\ResourceModel\ShareHistory\Collection as HistoryCollection;
use SM\ShoppingList\Model\ResourceModel\ShareHistory\CollectionFactory as HistoryCollectionFactory;
use SM\ShoppingList\Model\ResourceModel\Wishlist\Collection as ShoppingListCollection;
use SM\ShoppingList\Model\ResourceModel\Wishlist\CollectionFactory as ShoppingListCollectionFactory;

/**
 * Class ShoppingListRepository
 * @package SM\ShoppingList\Model
 */
class ShoppingListRepository implements ShoppingListRepositoryInterface
{
    /**
     * @var ShoppingListCollectionFactory
     */
    protected $shoppingListCollectionFactory;
    /**
     * @var ShoppingListDataInterfaceFactory
     */
    protected $shoppingListDataFactory;
    /**
     * @var ShoppingListItemCollectionFactory
     */
    protected $itemCollectionFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var ShoppingListFactory
     */
    protected $wishlistFactory;
    /**
     * @var HistoryCollectionFactory
     */
    protected $historyCollectionFactory;

    /**
     * @var ItemFactory
     */
    protected $itemFactory;
    /**
     * @var ShareHistoryFactory
     */
    protected $historyFactory;

    protected $historyResource;

    /**
     * @var \SM\ShoppingList\Helper\Data
     */
    protected $shoppingListHelper;

    /**
     * @var Converter
     */
    protected $converter;

    /**
     * @var \Magento\MultipleWishlist\Helper\Data
     */
    protected $wishlistData;

    /**
     * ShoppingListRepository constructor.
     * @param ShoppingListDataInterfaceFactory $shoppingListDataInterfaceFactory
     * @param ShoppingListCollectionFactory $shoppingListCollectionFactory
     * @param ShoppingListItemCollectionFactory $itemCollectionFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ShoppingListFactory $shoppingListFactory
     * @param HistoryCollectionFactory $historyCollectionFactory
     * @param ItemFactory $itemFactory
     * @param ShareHistoryFactory $historyFactory
     * @param ResourceModel\ShareHistory $historyResource
     * @param \SM\ShoppingList\Helper\Data $shoppingListHelper
     * @param Converter $converter
     * @param \Magento\MultipleWishlist\Helper\Data $wishlistData
     */
    public function __construct(
        ShoppingListDataInterfaceFactory $shoppingListDataInterfaceFactory,
        ShoppingListCollectionFactory $shoppingListCollectionFactory,
        ShoppingListItemCollectionFactory $itemCollectionFactory,
        DataObjectHelper $dataObjectHelper,
        ShoppingListFactory $shoppingListFactory,
        HistoryCollectionFactory $historyCollectionFactory,
        ItemFactory $itemFactory,
        ShareHistoryFactory $historyFactory,
        \SM\ShoppingList\Model\ResourceModel\ShareHistory $historyResource,
        \SM\ShoppingList\Helper\Data $shoppingListHelper,
        Converter $converter,
        \Magento\MultipleWishlist\Helper\Data $wishlistData
    ) {
        $this->wishlistData = $wishlistData;
        $this->converter = $converter;
        $this->shoppingListHelper = $shoppingListHelper;
        $this->historyResource = $historyResource;
        $this->historyFactory = $historyFactory;
        $this->itemFactory = $itemFactory;
        $this->shoppingListCollectionFactory = $shoppingListCollectionFactory;
        $this->shoppingListDataFactory = $shoppingListDataInterfaceFactory;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->wishlistFactory = $shoppingListFactory;
        $this->historyCollectionFactory = $historyCollectionFactory;
    }

    /**
     * @param ShoppingListDataInterface $shoppingList
     * @param int $customerId
     * @return ShoppingListDataInterface
     * @throws WebapiException
     */
    public function create(ShoppingListDataInterface $shoppingList, $customerId)
    {
        $currentNumber = $this->getCurrentShoppingListsNumber($customerId);
        $limit = $this->shoppingListHelper->getLimitShoppingListNumber();
        if ($limit - $currentNumber <= 0) {
            throw new WebapiException(
                __("Sorry, you have reached the maximum number of shopping lists"),
                0,
                WebapiException::HTTP_BAD_REQUEST
            );
        } elseif (!$this->isNameExist($shoppingList, $customerId)) {
            /** @var Wishlist $shoppingListModel */
            $shoppingListModel = $this->wishlistFactory->create();
            $shoppingListModel->setData([
                "customer_id" => $customerId,
                "name" => $shoppingList->getName(),
                "shared" => 1,
                "visibility" => 1
            ]);
            $shoppingListModel->generateSharingCode();
            $shoppingListModel->save();

            if ($shoppingListModel->getId()) {
                return $this->prepareDataToReturn($shoppingListModel);
            } else {
                throw new WebapiException(
                    __("Unable to create shopping list"),
                    0,
                    WebapiException::HTTP_BAD_REQUEST
                );
            }
        } else {
            throw new WebapiException(
                __("That shopping list name already exists. Please try again with another name"),
                0,
                WebapiException::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @param int $customerId
     * @return int
     */
    protected function getCurrentShoppingListsNumber($customerId)
    {
        /** @var ShoppingListCollection $shoppingCollection */
        $shoppingCollection = $this->shoppingListCollectionFactory->create();
        $shoppingCollection->addFieldToFilter("customer_id", ["eq" => $customerId]);
        $shoppingCollection->addFieldToFilter("name", ["neq" => "null"]);
        return $shoppingCollection->count();
    }

    /**
     * @param Wishlist $shoppingListModel
     * @return ShoppingListDataInterface
     */
    protected function prepareDataToReturn(Wishlist $shoppingListModel)
    {
        $shoppingListData = $this->shoppingListDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $shoppingListData,
            $shoppingListModel->getData(),
            ShoppingListDataInterface::class
        );

        return $shoppingListData;
    }

    /**
     * @param ShoppingListDataInterface $shoppingList
     * @param int $customerId
     * @return bool
     */
    protected function isNameExist(ShoppingListDataInterface $shoppingList, $customerId)
    {
        if ($shoppingList->getName() == $this->shoppingListHelper->getDefaultShoppingListName()) {
            return true;
        }

        /** @var ShoppingListCollection $collection */
        $shoppingListCollection = $this->shoppingListCollectionFactory->create();
        $shoppingListCollection->addFieldToFilter("name", ["eq" => $shoppingList->getName()]);
        $shoppingListCollection->addFieldToFilter("customer_id", ["eq" => $customerId]);

        if ($shoppingList->getWishlistId() != null) {
            $shoppingListCollection->addFieldToFilter("wishlist_id", ["neq" => $shoppingList->getWishlistId()]);
        }
        if ($shoppingListCollection->getSize()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int $shoppingListId
     * @return ShoppingListDataInterface
     * @throws WebapiException
     */
    public function getById($shoppingListId)
    {
        /** @var ShoppingListDataInterface $shoppingListData */
        $shoppingListData = $this->shoppingListDataFactory->create();
        /** @var Wishlist $wishlist */
        $wishlist = $this->validateShoppingList($shoppingListId);
        $this->dataObjectHelper->populateWithArray(
            $shoppingListData,
            $wishlist->getData(),
            ShoppingListDataInterface::class
        );

        if ($wishlist->getData("name") == null) {
            $shoppingListData->setIsDefault(1);
            $shoppingListData->setName($this->shoppingListHelper->getDefaultShoppingListName());
        } else {
            $shoppingListData->setIsDefault(0);
        }

        return $shoppingListData;
    }

    /**
     * Delete shopping list by ID.
     * @param int $shoppingListId
     * @return bool true on success
     * @throws WebapiException
     */
    public function delete($shoppingListId)
    {
        /** @var Wishlist $wishlist */
        $wishlist = $this->wishlistFactory->create()->load($shoppingListId);
        if ($wishlist->getId()) {
            if (is_null($wishlist->getData("name"))) {
                throw new WebapiException(
                    __("You can not delete " . $this->shoppingListHelper->getDefaultShoppingListName()),
                    0,
                    WebapiException::HTTP_BAD_REQUEST
                );
            } else {
                try {
                    $wishlist->delete();
                    return true;
                } catch (\Exception $e) {
                    throw new WebapiException(
                        __($e->getMessage()),
                        0,
                        WebapiException::HTTP_BAD_REQUEST
                    );
                }
            }
        }
        return false;
    }

    /**
     * @param int $shoppingListId
     * @return Wishlist
     * @throws WebapiException
     */
    public function validateShoppingList($shoppingListId)
    {
        /** @var Wishlist $shoppingListModel */
        $shoppingListModel = $this->wishlistFactory->create()->load($shoppingListId);
        if (!$shoppingListModel->getId()) {
            throw new WebapiException(
                __('Shopping list with id "%1" does not exist.', $shoppingListId),
                0,
                WebapiException::HTTP_BAD_REQUEST
            );
        }
        return $shoppingListModel;
    }

    /**
     * @param $sharingCode
     * @param int|null $customerId
     */
    public function updateHistory($sharingCode, $customerId = null)
    {
        /** @var HistoryCollection $historyCollection */
        $historyCollection = $this->historyCollectionFactory->create();
        if (!is_null($customerId)) {
            $historyCollection->addFieldToFilter("new_sharing_code", ["eq" => $sharingCode]);
            $historyCollection->addFieldToFilter("customer_id", ["eq" => $customerId]);
        } else {
            $historyCollection->addFieldToFilter("sharing_code", ["eq" => $sharingCode]);
        }
        $historyCollection->walk("delete");
    }

    /**
     * @param int $shoppingListId
     * @param int $customerId
     * @return ShoppingListDataInterface
     * @throws WebapiException
     */
    public function share($shoppingListId, $customerId)
    {
        /** @var ShoppingListDataInterface $listData */
        $listData = $this->getById($shoppingListId);

        $historyCollection = $this->historyCollectionFactory->create();
        $historyCollection->addFieldToFilter("customer_id", ["eq" => $customerId]);
        $historyCollection->addFieldToFilter("sharing_code", ["eq" => $listData->getSharingCode()]);
        if ($historyCollection->getSize()) {
            throw new WebapiException(
                __("This list has already been in your shopping list"),
                0,
                WebapiException::HTTP_BAD_REQUEST
            );
        } elseif ($listData->getCustomerId() == $customerId) {
            throw new WebapiException(
                __("This list has already been in your shopping list"),
                0,
                WebapiException::HTTP_BAD_REQUEST
            );
        } else {
            /** @var ShoppingListDataInterface $newListData */
            $newListData = $this->shoppingListDataFactory->create();

            $newListData->setName($listData->getName());

            if ($this->isNameExist($newListData, $customerId)) {
                $newListData = $this->getNewFileName($newListData);
            }

            /** @var ShoppingListDataInterface $newListData */
            $newListData = $this->create($newListData, $customerId);

            /** @var ItemCollection $itemCollection */
            $itemCollection = $this->itemCollectionFactory->create();
            $itemCollection->addFieldToFilter("wishlist_id", ["eq" => $shoppingListId]);

            /** @var Item $item */
            foreach ($itemCollection->getData() as $item) {
                $item = $this->itemFactory->create()->setData($item);
//                $this->addItem($item, $newListData->getWishlistId());
            }

            try {
                /** @var ShareHistory $history */
                $history = $this->historyFactory->create()
                    ->setCustomerId($customerId)
                    ->setSharingCode($listData->getSharingCode())
                    ->setNewSharingCode($newListData->getSharingCode())
                    ->setShoppinglistId($newListData->getWishlistId());
                $this->historyResource->save($history);
            } catch (AlreadyExistsException|Exception $e) {
                throw new WebapiException(
                    __($e->getMessage()),
                    0,
                    WebapiException::HTTP_BAD_REQUEST
                );
            }

            return $newListData;
        }
    }

    /**
     * Get new file name if the same is already exists
     *
     * @param ShoppingListDataInterface $shoppingListData
     * @return ShoppingListDataInterface
     */
    protected function getNewFileName(ShoppingListDataInterface $shoppingListData)
    {
        $index = 1;
        $shoppingListName = $shoppingListData->getName();

        if ($shoppingListName == $this->shoppingListHelper->getDefaultShoppingListName()) {
            $shoppingListData->setName($this->shoppingListHelper->getDefaultShoppingListName() . " (1)");
        }

        do {
            $shoppingListData->setName($shoppingListName . " (" . $index . ")");
            $index++;
        } while ($this->isNameExist($shoppingListData, $shoppingListData->getCustomerId()));

        return $shoppingListData;
    }

    /**
     * @param ShoppingListDataInterface $shoppingList
     * @param int $customerId
     * @return ShoppingListDataInterface
     * @throws WebapiException
     */
    public function update(ShoppingListDataInterface $shoppingList, $customerId)
    {
        if (!$this->isNameExist($shoppingList, $customerId)) {
            /** @var Wishlist $listModel */
            $listModel = $this->validateShoppingList($shoppingList->getWishlistId());
            $listModel->setData("name", $shoppingList->getName());
            try {
                $listModel->save();
            } catch (Exception $e) {
                throw new WebapiException(
                    __($e->getMessage()),
                    0,
                    WebapiException::HTTP_BAD_REQUEST
                );
            }

            return $this->prepareDataToReturn($listModel);
        } else {
            throw new WebapiException(
                __("Shopping list name is already exist. Please try again"),
                0,
                WebapiException::HTTP_BAD_REQUEST
            );
        }
    }


    /**
     * @inheritDoc
     * @throws WebapiException
     */
    public function getFavorites($customerId)
    {
        $wishlist = $this->wishlistData->getDefaultWishlist($customerId);
        if ($wishlist->getId()) {
            $listData = $this->converter->convertModelToResponse($wishlist);
            $listData->setName($this->shoppingListHelper->getDefaultShoppingListName());
            return $listData;
        } else {
            throw new WebapiException(
                __('Internal Error: User does not have default list'),
                0,
                WebapiException::HTTP_INTERNAL_ERROR
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function getMyList($customerId)
    {
        $collection = $this->wishlistData->getCustomerWishlists($customerId);
        $defaultId = $this->wishlistData->getDefaultWishlist($customerId)->getId();
        $collection->removeItemByKey($defaultId);

        return $this->converter->convertCollectionToResponse($collection);
    }

    /**
     * @inheritDoc
     * @throws WebapiException
     */
    public function getListDetail($listId)
    {
        $wishlist = $this->wishlistFactory->create()->load($listId);
        if ($wishlist->getId()) {
            return $this->converter->convertModelToResponse($wishlist);
        } else {
            throw new WebapiException(
                __('Shopping list with ID %1 is not exists', $listId),
                0,
                WebapiException::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function getAll($productId, $customerId)
    {
        $collection = $this->wishlistData->getCustomerWishlists($customerId);
        return $this->converter->convertCollectionToMinimalResponse($collection, $productId);
    }
}
