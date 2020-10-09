<?php

declare(strict_types=1);

namespace SM\Category\Model\Repository;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\CategorySearchResultsInterfaceFactory;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryList;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Eav\Model\Entity;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use SM\Category\Helper\Config;

class CategoryRepository extends CategoryList
{
    const MAIN_CATEGORIES_CACHE_KEY = 'main_categories_in_store';

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * CategoryRepository constructor.
     * @param CollectionFactory $categoryCollectionFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CategorySearchResultsInterfaceFactory $categorySearchResultsFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param StoreManagerInterface $storeManager
     * @param CacheInterface $cache
     * @param SerializerInterface $serializer
     * @param CollectionProcessorInterface|null $collectionProcessor
     */
    public function __construct(
        CollectionFactory $categoryCollectionFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CategorySearchResultsInterfaceFactory $categorySearchResultsFactory,
        CategoryRepositoryInterface $categoryRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        StoreManagerInterface $storeManager,
        CacheInterface $cache,
        SerializerInterface $serializer,
        CollectionProcessorInterface $collectionProcessor = null
    ) {
        parent::__construct(
            $categoryCollectionFactory,
            $extensionAttributesJoinProcessor,
            $categorySearchResultsFactory,
            $categoryRepository,
            $collectionProcessor
        );

        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->storeManager = $storeManager;
        $this->cache = $cache;
        $this->serializer = $serializer;
    }

    /**
     * @param int $storeId
     * @return CategoryInterface[]
     * @throws LocalizedException
     */
    public function getCategoriesInSearchForm(int $storeId = null): array
    {
        /** @var Store $store */
        $store = $this->storeManager->getStore($storeId);
        $cacheKey = self::MAIN_CATEGORIES_CACHE_KEY . $store->getId();
        $categories = $this->cache->load($cacheKey);

        if (!$this->cache->load($cacheKey)) {
            $sortByPosition = $this->sortOrderBuilder
                ->setField(CategoryInterface::KEY_POSITION)
                ->setAscendingDirection()
                ->create();

            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(CategoryInterface::KEY_LEVEL, 2)
                ->addFilter(CategoryInterface::KEY_IS_ACTIVE, true)
                ->addFilter(CategoryInterface::KEY_PARENT_ID, $store->getRootCategoryId())
                ->addFilter(Config::INCLUDE_IN_SEARCH_FORM_ATTRIBUTE_CODE, true)
                ->setSortOrders([$sortByPosition])
                ->create();

            $searchResult = $this->getList($searchCriteria);

            $categoriesData = [];
            /** @var Category $category */
            foreach ($searchResult->getItems() as $category) {
                $categoriesData[$category->getId()] = $category->toArray();
            }

            $this->cache->save($this->serializer->serialize($categoriesData), $cacheKey);
        } else {
            $categoriesData = $this->serializer->unserialize($categories);
        }

        return $categoriesData;
    }

    /**
     * @param int[] $ids
     * @return CategoryInterface[]
     */
    public function getCategoriesByIds(array $ids): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(Entity::DEFAULT_ENTITY_ID_FIELD, $ids, 'in')
            ->create();

        $searchResult = $this->getList($searchCriteria);

        return $searchResult->getItems();
    }
}
