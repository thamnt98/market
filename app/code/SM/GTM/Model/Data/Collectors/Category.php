<?php
declare(strict_types=1);

namespace SM\GTM\Model\Data\Collectors;

use Magento\Catalog\Api\CategoryListInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use SM\GTM\Api\CollectorInterface;
use SM\GTM\Api\MapperInterface;

/**
 * Class Category
 * @package SM\GTM\Model\Data\Collectors
 */
class Category implements CollectorInterface
{
    /**
     * @var string
     */
    const CURRENT_CATEGORY_REGISTRY_KEY = 'current_category';
    /**
     * @var string
     */
    const PARENT_CATEGORY_KEY = 'parent_category';
    /**
     * @var string
     */
    const CATEGORY_LEVEL_1_KEY = 'category_level_1';

    /**
     * @var string
     */
    const CATEGORY_LEVEL_2_KEY = 'category_level_2';
    /**
     * @var CategoryInterface|null
     */
    private $category = null;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var MapperInterface
     */
    private $categoryMapper;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var array
     */
    private $cachedCategories = [];

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CategoryListInterface
     */
    private $categoryList;

    /**
     * Category constructor.
     * @param MapperInterface $categoryMapper
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CategoryListInterface $categoryList
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Registry $registry
     */
    public function __construct(
        MapperInterface $categoryMapper,
        CategoryRepositoryInterface $categoryRepository,
        CategoryListInterface $categoryList,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Registry $registry
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryList = $categoryList;
        $this->categoryMapper = $categoryMapper;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->registry = $registry;
    }

    /**
     * @return CategoryInterface|null
     */
    public function getCategory()
    {
        if ($this->category === null) {
            $category = $this->registry->registry(self::CURRENT_CATEGORY_REGISTRY_KEY);

            if ($category) {
                try {
                    $parentCategory = $this->categoryRepository->get($category->getParentId());
                    $category->setData(self::PARENT_CATEGORY_KEY, $parentCategory);
                    $category->setData(self::CATEGORY_LEVEL_1_KEY, $this->getCategoryByLevel($category, 2));
                    $category->setData(self::CATEGORY_LEVEL_2_KEY, $this->getCategoryByLevel($category, 3));
                } catch (NoSuchEntityException $noSuchEntityException) {
                    $category->setData(self::PARENT_CATEGORY_KEY, null);
                }
            }

            $this->category = $category;
        }

        return $this->category;
    }

    /**
     * @param CategoryInterface|null $category
     * @return Category
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function collect()
    {
        return $this->categoryMapper->map($this->getCategory())->toArray();
    }

    /**
     * @param CategoryInterface $category
     * @param int $level
     * @return CategoryInterface|null
     */
    private function getCategoryByLevel($category, int $level)
    {
        $cacheKey = sprintf('%s|%s', $category->getId(), $level);
        if (!array_key_exists($cacheKey, $this->cachedCategories)) {
            $this->cachedCategories[$cacheKey] = null;
            $categoryPath = explode('/', $category->getPath());

            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('entity_id', $categoryPath, 'in')
                ->addFilter('level', $level, 'eq')
                ->create();
            $item = $this->categoryList->getList($searchCriteria)->getItems();
            $this->cachedCategories[$cacheKey] = $item[0] ?? null;
        }
        return $this->cachedCategories[$cacheKey] ?? null;
    }
}
