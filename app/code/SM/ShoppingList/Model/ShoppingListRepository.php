<?php

namespace SM\ShoppingList\Model;

use BadMethodCallException;
use Exception;
use LengthException;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\Search\SearchCriteriaInterfaceFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DB\Adapter\DuplicateException;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Stdlib\DateTime;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\ScopeInterface;
use Magento\Wishlist\Model\Item;
use Magento\Wishlist\Model\ItemFactory;
use Magento\Wishlist\Model\ResourceModel\Item\Collection as ItemCollection;
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\WishlistFactory as ShoppingListFactory;
use SM\ShoppingList\Api\Data\ResultDataInterfaceFactory;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;
use SM\ShoppingList\Api\Data\ShoppingListDataInterfaceFactory;
use SM\ShoppingList\Api\Data\ShoppingListItemDataInterface;
use SM\ShoppingList\Api\Data\ShoppingListItemDataInterfaceFactory;
use SM\ShoppingList\Api\Data\ShoppingListSearchResultsInterface;
use SM\ShoppingList\Api\Data\ShoppingListSearchResultsInterfaceFactory;
use SM\ShoppingList\Api\ShoppingListRepositoryInterface;
use SM\ShoppingList\Model\ResourceModel\Item\Collection as ShoppingListItemCollection;
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
     * @var SearchCriteriaInterfaceFactory
     */
    protected $searchResultsFactory;
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
     * @var ShoppingListItemDataInterfaceFactory
     */
    protected $shoppingListItemDataInterfaceFactory;
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var ShoppingListItemDataInterfaceFactory
     */
    protected $shoppingListItemDataFactory;
    /**
     * @var ShoppingListFactory
     */
    protected $shoppingListFactory;
    /**
     * @var HistoryCollectionFactory
     */
    protected $historyCollectionFactory;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var ResultDataInterfaceFactory
     */
    protected $resultDataFactory;
    /**
     * @var Data
     */
    protected $priceHelper;
    /**
     * @var ReviewFactory
     */
    protected $reviewFactory;
    /**
     * @var ProductRepository
     */
    protected $productRepository;
    /**
     * @var Image
     */
    protected $imageHelper;
    /**
     * @var Emulation
     */
    protected $appEmulation;
    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;
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
     * ShoppingListRepository constructor.
     * @param ShoppingListSearchResultsInterfaceFactory $searchCriteriaInterfaceFactory
     * @param ShoppingListDataInterfaceFactory $shoppingListDataInterfaceFactory
     * @param ShoppingListCollectionFactory $shoppingListCollectionFactory
     * @param ShoppingListItemCollectionFactory $itemCollectionFactory
     * @param ShoppingListItemDataInterfaceFactory $shoppingListItemDataInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ShoppingListItemDataInterfaceFactory $shoppingListItemDataFactory
     * @param ShoppingListFactory $shoppingListFactory
     * @param HistoryCollectionFactory $historyCollectionFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param ResultDataInterfaceFactory $resultDataFactory
     * @param Data $priceHelper
     * @param ReviewFactory $reviewFactory
     * @param ProductRepository $productRepository
     * @param Image $imageHelper
     * @param Emulation $appEmulation
     * @param CollectionProcessorInterface $processor
     * @param ItemFactory $itemFactory
     * @param ShareHistoryFactory $historyFactory
     * @param ResourceModel\ShareHistory $historyResource
     */
    public function __construct(
        ShoppingListSearchResultsInterfaceFactory $searchCriteriaInterfaceFactory,
        ShoppingListDataInterfaceFactory $shoppingListDataInterfaceFactory,
        ShoppingListCollectionFactory $shoppingListCollectionFactory,
        ShoppingListItemCollectionFactory $itemCollectionFactory,
        ShoppingListItemDataInterfaceFactory $shoppingListItemDataInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        ShoppingListItemDataInterfaceFactory $shoppingListItemDataFactory,
        ShoppingListFactory $shoppingListFactory,
        HistoryCollectionFactory $historyCollectionFactory,
        ScopeConfigInterface $scopeConfig,
        ResultDataInterfaceFactory $resultDataFactory,
        Data $priceHelper,
        ReviewFactory $reviewFactory,
        ProductRepository $productRepository,
        Image $imageHelper,
        Emulation $appEmulation,
        CollectionProcessorInterface $processor,
        ItemFactory $itemFactory,
        ShareHistoryFactory $historyFactory,
        \SM\ShoppingList\Model\ResourceModel\ShareHistory $historyResource
    ) {
        $this->historyResource = $historyResource;
        $this->historyFactory = $historyFactory;
        $this->itemFactory = $itemFactory;
        $this->collectionProcessor = $processor;
        $this->appEmulation = $appEmulation;
        $this->imageHelper = $imageHelper;
        $this->reviewFactory = $reviewFactory;
        $this->productRepository = $productRepository;
        $this->priceHelper = $priceHelper;
        $this->resultDataFactory = $resultDataFactory;
        $this->scopeConfig = $scopeConfig;
        $this->searchResultsFactory = $searchCriteriaInterfaceFactory;
        $this->shoppingListCollectionFactory = $shoppingListCollectionFactory;
        $this->shoppingListDataFactory = $shoppingListDataInterfaceFactory;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->shoppingListItemDataInterfaceFactory = $shoppingListItemDataInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->shoppingListItemDataFactory = $shoppingListItemDataFactory;
        $this->shoppingListFactory = $shoppingListFactory;
        $this->historyCollectionFactory = $historyCollectionFactory;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param int $customerId
     * @return ShoppingListSearchResultsInterface
     * @throws NoSuchEntityException
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $customerId)
    {
        /** @var ShoppingListSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $listCollection = $this->shoppingListCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $listCollection);
        $listCollection
            ->addFieldToFilter("customer_id", ["eq" => $customerId])
            ->setCurPage($searchCriteria->getCurrentPage())
            ->setPageSize($searchCriteria->getPageSize());
        $searchResults->setTotalCount($listCollection->getSize());
        $shoppingLists = [];
        /** @var Wishlist $shoppingList */
        foreach ($listCollection as $shoppingList) {
            $shoppingLists[] = $this->listProcess($shoppingList);
        }

        $searchResults->setTotalCount($listCollection->getSize());
        $searchResults->setItems($shoppingLists);
        return $searchResults;
    }

    /**
     * @param Wishlist $shoppingList
     * @return ShoppingListDataInterface
     * @throws NoSuchEntityException
     */
    public function listProcess($shoppingList)
    {
        /** @var ShoppingListDataInterface $shoppingListData */
        $shoppingListData = $this->shoppingListDataFactory->create();

        $this->dataObjectHelper->populateWithArray(
            $shoppingListData,
            $shoppingList->getData(),
            "SM\ShoppingList\Api\Data\ShoppingListDataInterface"
        );

        $items = [];
        /** @var ShoppingListItemCollection $itemCollection */
        $itemCollection = $this->itemCollectionFactory->create();
        $itemCollection->addFieldToFilter("wishlist_id", ["eq" => $shoppingList->getId()]);
        foreach ($itemCollection->getData() as $item) {
            /** @var ShoppingListItemDataInterface $itemData */
            $itemData = $this->shoppingListItemDataFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $itemData,
                $item,
                "SM\ShoppingList\Api\Data\ShoppingListDataInterface"
            );

            /** @var ShoppingListItemDataInterface $itemData */
            $items[] = $this->getProductInfo($item, $itemData);
        }
        if (is_null($shoppingList->getData("name"))) {
            $shoppingListData->setName($this->getDefaultShoppingListName());
            $shoppingListData->setIsDefault(1);
        } else {
            $shoppingListData->setIsDefault(0);
        }
        $shoppingListData->setItems($items);
        return $shoppingListData;
    }

    /**
     * @param $item
     * @param ShoppingListItemDataInterface $itemData
     * @return ShoppingListItemDataInterface
     * @throws NoSuchEntityException
     */
    protected function getProductInfo($item, $itemData)
    {
        $this->appEmulation->startEnvironmentEmulation(
            $item["store_id"],
            Area::AREA_FRONTEND,
            true
        );
        /** @var Product $product */
        $product = $this->productRepository->getById($item["product_id"]);
        $itemData->setCustomAttribute(
            "product_image",
            $this->imageHelper->init($product, "product_base_image")->getUrl()
        );

        $this->appEmulation->stopEnvironmentEmulation();
        return $itemData;
    }

    /**
     * @param ShoppingListDataInterface $shoppingList
     * @param int $customerId
     * @return ShoppingListDataInterface
     * @throws DuplicateException
     * @throws LengthException
     * @throws Exception
     */
    public function create(ShoppingListDataInterface $shoppingList, $customerId)
    {
        $currentNumber = $this->getCurrentShoppingListsNumber($customerId);
        $limit = $this->getLimitShoppingListNumber();
        if ($limit - $currentNumber <= 0) {
            throw new LengthException(__("You have reached maximum shopping list number"));
        } elseif (!$this->isNameExist($shoppingList, $customerId)) {
            /** @var Wishlist $shoppingListModel */
            $shoppingListModel = $this->shoppingListFactory->create();
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
                throw new Exception(__("Unable to create shopping list"));
            }
        } else {
            throw new DuplicateException(__("Shopping list name is already exist. Please try again"));
        }
    }

    /**
     * @return string
     */
    public function getDefaultShoppingListName()
    {
        return __("My Favorites");
    }

    /**
     * @return int
     */
    public function getLimitShoppingListNumber()
    {
        return $this->scopeConfig->getValue(
            'shoppinglist/general/shopping_list_number',
            ScopeInterface::SCOPE_STORE
        );
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
        if ($shoppingList->getName() == $this->getDefaultShoppingListName()) {
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
     * @throws NoSuchEntityException
     */
    public function getById($shoppingListId)
    {
        /** @var ShoppingListDataInterface $shoppingListData */
        $shoppingListData = $this->shoppingListDataFactory->create();
        /** @var Wishlist $shoppingList */
        $shoppingList = $this->validateShoppingList($shoppingListId);
        $this->dataObjectHelper->populateWithArray(
            $shoppingListData,
            $shoppingList->getData(),
            'SM\ShoppingList\Api\Data\ShoppingListDataInterface'
        );

        if ($shoppingList->getData("name") == null) {
            $shoppingListData->setIsDefault(1);
            $shoppingListData->setName($this->getDefaultShoppingListName());
        } else {
            $shoppingListData->setIsDefault(0);
        }

        return $shoppingListData;
    }

    /**
     * Delete shopping list by ID.
     * @param int $shoppingListId
     * @return bool true on success
     * @throws BadMethodCallException
     * @throws Exception
     */
    public function delete($shoppingListId)
    {
        /** @var Wishlist $shoppinglistModel */
        $shoppinglistModel = $this->shoppingListFactory->create()->load($shoppingListId);
        if ($shoppinglistModel->getId()) {
            if (is_null($shoppinglistModel->getData("name"))) {
                throw new BadMethodCallException(__("You can not delete " . $this->getDefaultShoppingListName()));
            } else {
                $this->updateHistory($shoppinglistModel->getSharingCode(), $shoppinglistModel->getCustomerId());
                $shoppinglistModel->delete();
                return true;
            }
        }
        return false;
    }

    /**
     * @param int $shoppingListId
     * @return Wishlist
     * @throws NoSuchEntityException
     */
    public function validateShoppingList($shoppingListId)
    {
        /** @var Wishlist $shoppingListModel */
        $shoppingListModel = $this->shoppingListFactory->create()->load($shoppingListId);
        if (!$shoppingListModel->getId()) {
            throw new NoSuchEntityException(__('Shopping list with id "%1" does not exist.', $shoppingListId));
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
     * @throws CouldNotSaveException
     * @throws DuplicateException
     * @throws NoSuchEntityException
     */
    public function share($shoppingListId, $customerId)
    {
        /** @var ShoppingListDataInterface $listData */
        $listData = $this->getById($shoppingListId);

        $historyCollection = $this->historyCollectionFactory->create();
        $historyCollection->addFieldToFilter("customer_id", ["eq" => $customerId]);
        $historyCollection->addFieldToFilter("sharing_code", ["eq" => $listData->getSharingCode()]);
        if ($historyCollection->getSize()) {
            throw new DuplicateException(__("This list has already been in your shopping list"));
        } elseif ($listData->getCustomerId() == $customerId) {
            throw new DuplicateException(__("This list has already been in your shopping list"));
        } else {
            /** @var ShoppingListDataInterface $newListData */
            $newListData = $this->shoppingListDataFactory->create();

            $newListData->setName($listData->getName());

            if ($this->isNameExist($newListData, $customerId)) {
                $newListData = $this->getNewFileName($newListData);
            }

            /** @var ShoppingListDataInterface $newListData */
            try {
                $newListData = $this->create($newListData, $customerId);
            } catch (LengthException $e) {
                throw new LengthException(__("You have reached maximum shopping list number"));
            } catch (DuplicateException|Exception $e) {
                throw new CouldNotSaveException(__("Some thing went wrong while saving this list"));
            }

            /** @var ItemCollection $itemCollection */
            $itemCollection = $this->itemCollectionFactory->create();
            $itemCollection->addFieldToFilter("wishlist_id", ["eq" => $shoppingListId]);

            /** @var Item $item */
            foreach ($itemCollection->getData() as $item) {
                $item = $this->itemFactory->create()->setData($item);
                $this->addItem($item, $newListData->getWishlistId());
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
                throw new CouldNotSaveException(__("Some thing went wrong while saving this list"));
            }

            return $newListData;
        }
    }

    /**
     * @param Item $item
     * @param int $shoppingListId
     * @return bool
     */
    private function addItem($item, $shoppingListId)
    {
        try {
            $this->itemFactory->create()
                ->setProductId($item->getProductId())
                ->setWishlistId($shoppingListId)
                ->setAddedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT))
                ->setStoreId($item->getStoreId())
                ->setQty(1)
                ->save();
            return true;
        } catch (Exception $e) {
            return false;
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

        if ($shoppingListName == $this->getDefaultShoppingListName()) {
            $shoppingListData->setName($this->getDefaultShoppingListName() . " (1)");
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
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function update(ShoppingListDataInterface $shoppingList, $customerId)
    {
        if (!$this->isNameExist($shoppingList, $customerId)) {
            /** @var Wishlist $listModel */
            $listModel = $this->validateShoppingList($shoppingList->getWishlistId());
            $listModel->setData("name", $shoppingList->getName());
            $this->updateHistory($listModel->getSharingCode());
            $listModel->generateSharingCode()->save();

            return $this->prepareDataToReturn($listModel);
        } else {
            throw new Exception(__("Shopping list name is already exist. Please try again"));
        }
    }
}
