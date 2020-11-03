<?php

namespace SM\Category\Model\Catalog;

use Magento\Store\Model\ScopeInterface;
use SM\Category\Api\Data\Catalog\CategoryMetaDataInterface;
use SM\HeroBanner\Model\Banner;
use SM\MobileApi\Api\Data\Product\ListInterface;

class Tree
{
    const GET_ALL_ACTIVE = true;

    protected $_categoryHelper;

    protected $categoryFlatConfig;

    protected $categoryInfoFactory;

    protected $categoryTreeFactory;

    protected $categoryFactory;

    protected $dataCollectionFactory;

    protected $dataObjectProcessor;

    protected $dataObjectHelper;

    protected $storeManager;

    protected $request;

    protected $productStatus;

    protected $productVisibility;

    protected $imageHelper;

    protected $scopeConfig;

    protected $mProductHelper;

    protected $categoryCollectionFactory;

    protected $productInterface;

    protected $bannerModel;

    protected $categoryColorFactory;

    protected $treeFactory;

    protected $categoryMetaData;

    public function __construct(
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\CollectionFactory $dataCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \SM\Category\Model\Data\Catalog\CategoryFactory $japiCategoryFactory,
        \SM\Category\Model\Data\Catalog\CategoryTreeFactory $categoryTreeFactory,
        \SM\MobileApi\Helper\Product $productHelper,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \SM\MobileApi\Api\ProductInterface $productInterface,
        Banner $bannerModel,
        \SM\Category\Model\Data\Catalog\CategoryColorFactory $categoryColorFactory,
        \Magento\Catalog\Model\ResourceModel\Category\TreeFactory $treeFactory,
        \SM\Category\Api\Data\Catalog\CategoryMetaDataInterfaceFactory $categoryMetaData
    ) {
        $this->_categoryHelper              = $categoryHelper;
        $this->categoryFlatConfig           = $categoryFlatState;
        $this->categoryFactory              = $categoryFactory;
        $this->dataCollectionFactory        = $dataCollectionFactory;
        $this->storeManager                 = $storeManager;
        $this->categoryInfoFactory          = $japiCategoryFactory;
        $this->categoryTreeFactory          = $categoryTreeFactory;
        $this->dataObjectProcessor          = $dataObjectProcessor;
        $this->dataObjectHelper             = $dataObjectHelper;
        $this->request                      = $request;
        $this->productStatus                = $productStatus;
        $this->productVisibility            = $productVisibility;
        $this->imageHelper                  = $imageHelper;
        $this->scopeConfig                  = $scopeConfig;
        $this->mProductHelper               = $productHelper;
        $this->categoryCollectionFactory    = $categoryCollectionFactory;
        $this->productInterface             = $productInterface;
        $this->bannerModel                  = $bannerModel;
        $this->categoryColorFactory         = $categoryColorFactory;
        $this->treeFactory                  = $treeFactory;
        $this->categoryMetaData             = $categoryMetaData;
    }

    /**
     * Get C0 Category
     * @return \SM\Category\Model\Data\Catalog\CategoryTree
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Db_Select_Exception
     */
    public function getTree()
    {
        $parentId   = $this->storeManager->getStore()->getRootCategoryId();
        $categories = [];
        /** @var \SM\Category\Model\Data\Catalog\CategoryTree $result */
        $result = $this->categoryTreeFactory->create();

        $rootCategory = $this->categoryFactory->create()->load($parentId);
        if (! $rootCategory->getId()) {
            $result->setCategories($categories);

            return $result;
        }

        $categories = $this->_getActiveCategories($rootCategory->getId());
        $result->setCategories($categories);

        return $result;
    }

    /**
     * @param $categoryId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSubCategoryById($categoryId)
    {
        $category           = $this->categoryFactory->create();
        $categoryCollection = $this->categoryCollectionFactory->create();
        $data = [];

        $cate = $categoryCollection->addAttributeToFilter('entity_id', ['eq' => $categoryId]);

        if ($cate->getSize() == 0) {
            return $data;
        }

        $categories = $category->getCategories($categoryId, 1, false, true, false);
        $categories->addAttributeToSelect('*');
        $categories->addAttributeToFilter('is_active', ['eq' => 1]);
        $categories->addAttributeToFilter('include_in_menu', ['eq' => 1]);
        $categories->setOrder('position', 'ASC');

        foreach ($categories as $cat) {
            $subCateInfo = $this->categoryInfoFactory->create();
            $subCateInfo->setEntityId($cat->getId());
            $subCateInfo->setName($cat->getName());
            $subCateInfo->setAttributeSetId($cat->getAttributeSetId());
            $subCateInfo->setParentId($cat->getParentId());
            $subCateInfo->setCreatedAt($cat->getCreatedAt());
            $subCateInfo->setUpdatedAt($cat->getUpdatedAt());
            $subCateInfo->setPosition($cat->getPosition());
            $subCateInfo->setPath($cat->getPath());
            $subCateInfo->setLevel($cat->getLevel());
            $subCateInfo->setImage($cat->getImageUrl());
            $subCateInfo->setIsActive($cat->getIsActive());
            $subCateInfo->setIsAnchor($cat->getIsAnchor());
            $subCateInfo->setCategoryId($cat->getId());
            $subCateInfo->setColor($this->getColorCategory($cat));
            $subCateInfo->setIsAlcohol($cat->getData(ListInterface::IS_ALCOHOL));
            $subCateInfo->setIsTobacco($cat->getData(ListInterface::IS_TOBACCO));
            $data[] = $subCateInfo;
        }

        return $data;
    }

    /**
     * @param $categoryId
     * @return CategoryMetaDataInterface|null
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function getCategoryMetaData($categoryId)
    {
        try {
            $category = $this->categoryFactory->create()->load($categoryId);
            if ($category->getId()) {
                $metaInfo = $this->categoryMetaData->create();
                $metaInfo->setEntityId($category->getId());
                $metaInfo->setGallery($this->bannerModel->getBannersByCategoryId($category->getId()));
                $metaInfo->setColor($this->getColorCategory($category));
                $metaInfo->setIsAlcohol($category->getData(CategoryMetaDataInterface::IS_FRESH));
                $metaInfo->setIsTobacco($category->getData(CategoryMetaDataInterface::IS_FRESH));
                $metaInfo->setIsFresh($category->getData(CategoryMetaDataInterface::IS_FRESH) ?? false);
                return $metaInfo;
            } else {
                return null;
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Webapi\Exception(__($e->getMessage()), 0, 404);
        }
    }

    /**
     * @param $categoryId
     * @return ListInterface|array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMostPopular($categoryId)
    {
        $tree = $this->treeFactory->create();
        $tree->loadNode($categoryId)->loadChildren(1)->getChildren();
        $tree->addCollectionData(null, true, $categoryId, true, false);

        $categories = $tree->getCollection();
        $categories->addAttributeToSelect('*');
        $categories->addAttributeToFilter('most_popular', ['eq' => 1]);
        $mostPopularCategoryId = null;
        if (empty($categories->getData())) {
            return [];
        }

        foreach ($categories->getData() as $category) {
            $mostPopularCategoryId = (int)$category['entity_id'];
            break;
        }

        return $this->productInterface->getList($mostPopularCategoryId, $limit = 10, $p = 1);
    }

    /**
     * @param $category
     * @return \SM\Category\Model\Data\Catalog\CategoryColor
     */
    public function getColorCategory($category)
    {
        $categoryColorObject = $this->categoryColorFactory->create();
        $categoryColorObject->setCategoryColor($category->getData(\SM\Category\Helper\Config::MAIN_CATEGORY_COLOR));
        $categoryColorObject->setMostPopularColor($category->getData(\SM\Category\Helper\Config::SUB_CATEGORY_COLOR));
        $categoryColorObject->setFavoriteBrandColor($category->getData(\SM\Category\Helper\Config::FAVORITE_BRAND_COLOR));
        $categoryColorObject->setProductColor($category->getData(\SM\Category\Helper\Config::PRODUCT_CATEGORY_COLOR));

        return $categoryColorObject;
    }

    /**
     * @param $parentId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    protected function _getActiveCategories($parentId)
    {
        $category   = $this->categoryFactory->create();
        $categories = $category->getCategories($parentId, 1, false, true, false);
        $categories->addAttributeToSelect('*');
        $categories->addAttributeToFilter('include_in_menu', ['eq' => 1]);
        $categories->addAttributeToFilter('is_active', ['eq' => 1]);
        $categories->setOrder('position', 'ASC');

        // Process include_in_menu
        if (self::GET_ALL_ACTIVE) {
            $wherePart = $categories->getSelect()->getPart(\Magento\Framework\DB\Select::WHERE);
            foreach ($wherePart as $i => $condition) {
                if (strpos($condition, 'at_include_in_menu') !== false) {
                    unset($wherePart[ $i ]);
                    break;
                }
            }
            $categories->getSelect()->setPart(\Magento\Framework\Db\Select::WHERE, $wherePart);
        }

        $categories->load();

        $data  = [];
        foreach ($categories as $cat) {
            /* @var  $cateInfo \SM\Category\Api\Data\Catalog\CategoryInterface*/
            /* @var $cat \Magento\Catalog\Api\Data\CategoryTreeInterface */
            $cateInfo = $this->categoryInfoFactory->create();
            $cateInfo->setEntityId($cat->getId());
            $cateInfo->setName($cat->getName());
            $cateInfo->setAttributeSetId($cat->getAttributeSetId());
            $cateInfo->setParentId($cat->getParentId());
            $cateInfo->setCreatedAt($cat->getCreatedAt());
            $cateInfo->setUpdatedAt($cat->getUpdatedAt());
            $cateInfo->setPosition($cat->getPosition());
            $cateInfo->setPath($cat->getPath());
            $cateInfo->setLevel($cat->getLevel());
            $cateInfo->setImage($cat->getImageUrl());
            $cateInfo->setIsActive($cat->getIsActive());
            $cateInfo->setIsAnchor($cat->getIsAnchor());
            $cateInfo->setCategoryId($cat->getId());
            $cateInfo->setColor($this->getColorCategory($cat));
            $cateInfo->setIsDigital($this->getIsDigital($cat));
            $cateInfo->setIsAlcohol($cat->getData("is_alcohol"));
            $cateInfo->setIsTobacco($cat->getData("is_tobacco"));
            $cateInfo->setIsFresh($cat->getData(\SM\Category\Api\Data\Catalog\CategoryInterface::IS_FRESH) ?? false);
            $data[] = $cateInfo;
        }

        return $data;
    }

    /**
     * @param \Magento\Catalog\Api\Data\CategoryTreeInterface $cat
     * @return bool
     */
    private function getIsDigital(\Magento\Catalog\Api\Data\CategoryTreeInterface $cat)
    {
        if ($this->scopeConfig->isSetFlag("digital_product/general/enable")) {
            return $cat->getId() == $this->scopeConfig->getValue(
                'digital_product/general/c0_category',
                ScopeInterface::SCOPE_STORES
            );
        } else {
            return false;
        }
    }
}
