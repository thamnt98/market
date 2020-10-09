<?php

namespace SM\Category\Model\Catalog;

use Magento\Checkout\Exception;
use Magento\Store\Model\ScopeInterface;
use SM\HeroBanner\Model\Banner;
use SM\MobileApi\Api\Data\Product\ListInterface;

class Tree
{
    const IMAGE_TYPE = 'thumbnail';

    const GET_ALL_ACTIVE = true;

    const MAX_TREE_DEPTH = 2;

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

    public function getParentCategories()
    {
        $parentId   = $this->storeManager->getStore()->getRootCategoryId();
        $categories = [];
        $result = $this->categoryTreeFactory->create();

        $rootCategory = $this->categoryFactory->create()->load($parentId);
        if (! $rootCategory->getId()) {
            $result->setCategories($categories);

            return $result;
        }

        $categories = $this->_getActiveCategories($rootCategory->getId(), 1, false);
        $result->setCategories($categories);

        return $result;
    }

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
            $subCateInfo->setEntityTypeId($cat->getEntityTypeId());
            $subCateInfo->setParentId($cat->getParentId());
            $subCateInfo->setCreatedAt($cat->getCreatedAt());
            $subCateInfo->setUpdatedAt($cat->getUpdatedAt());
            $subCateInfo->setPosition($cat->getPosition());
            $subCateInfo->setPath($cat->getPath());
            $subCateInfo->setLevel($cat->getLevel());
            $subCateInfo->setImage($cat->getImageUrl());
            $subCateInfo->setThumbnail($this->_getThumbnailFromProduct($cat));
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

    public function getCategoryMetaData($categoryId){
        try {
            $category = $this->categoryFactory->create()->load($categoryId);
            if ($category->getId()) {
                $metaInfo = $this->categoryMetaData->create();
                $metaInfo->setEntityId($category->getId());
                $metaInfo->setGallery($this->bannerModel->getBannersByCategoryId($category->getId()));
                $metaInfo->setColor($this->getColorCategory($category));
                return $metaInfo;
            } else {
                return null;
            }
        }catch (\Exception $e){
            throw new \Magento\Framework\Webapi\Exception(__($e->getMessage()),426,404);
        }
    }

    /**
     * @param $categoryId
     * @return \SM\MobileApi\Api\Data\Product\ListInterface|array
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

    public function getFavoriteBrand()
    {
        return $this->_getGalleryCategory();
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

    protected function _getActiveCategories($parentId, $indexLevel = 1, $includingProducts = true)
    {
        $maxTreeDepth = $this->request->getParam('max_tree_depth', null);
        if (! $maxTreeDepth) {
            $maxTreeDepth = self::MAX_TREE_DEPTH;
        }

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
        $index = 0;
        foreach ($categories as $cat) {
            /* @var  $cateInfo \SM\Category\Api\Data\Catalog\CategoryInterface*/
            /* @var $cat \Magento\Catalog\Api\Data\CategoryTreeInterface */
            $cateInfo = $this->categoryInfoFactory->create();
            $cateInfo->setEntityId($cat->getId());
            $cateInfo->setName($cat->getName());
            $cateInfo->setAttributeSetId($cat->getAttributeSetId());
            $cateInfo->setEntityTypeId($cat->getEntityTypeId());
            $cateInfo->setParentId($cat->getParentId());
            $cateInfo->setCreatedAt($cat->getCreatedAt());
            $cateInfo->setUpdatedAt($cat->getUpdatedAt());
            $cateInfo->setPosition($cat->getPosition());
            $cateInfo->setPath($cat->getPath());
            $cateInfo->setLevel($cat->getLevel());
            $cateInfo->setImage($cat->getImageUrl());
            $cateInfo->setThumbnail($this->_getThumbnailFromProduct($cat));
            $cateInfo->setIsActive($cat->getIsActive());
            $cateInfo->setIsAnchor($cat->getIsAnchor());
            $cateInfo->setCategoryId($cat->getId());
            $cateInfo->setColor($this->getColorCategory($cat));
            $cateInfo->setIsDigital($this->getIsDigital($cat));
            $cateInfo->setIsAlcohol($cat->getData("is_alcohol"));
            $cateInfo->setIsTobacco($cat->getData("is_tobacco"));

            $cateInfo->setIsFresh($cat->getData(\SM\Category\Api\Data\Catalog\CategoryInterface::IS_FRESH) ?? false);
            if ($includingProducts) {
                $products = $this->_getProductsFromCategory($cat);
                $cateInfo->setProducts($products);
            } else {
                $cateInfo->setProducts(null);
            }

            $data[ $index ] = $cateInfo;
            $index ++;
        }

        return $data;
    }

    protected function _getThumbnailFromProduct(\Magento\Catalog\Model\Category $category)
    {
        $_imageWidth  = 400;
        $_imageHeight = 400;

        $imageUrl = '';

        /** @var  \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
        $productCollection = $category->getProductCollection();
        $productCollection->addAttributeToSelect('*');
        $productCollection->addAttributeToFilter('status', [ 'in' => $this->productStatus->getVisibleStatusIds() ]);
        $productCollection->addAttributeToFilter('visibility', [ 'in' => $this->productVisibility->getVisibleInSiteIds() ]);
        $productCollection->getSelect()->limit(3);
        $productCollection->load();
        if (! $productCollection->getSize()) {
            return $imageUrl;
        }

        /** @var \Magento\Catalog\Model\Product\Interceptor $product */
        foreach ($productCollection as $product) {
            if (! $product->getData(self::IMAGE_TYPE)) {
                continue;
            }
            $_helper  = $this->imageHelper->init($product, '');
            $imageUrl = $_helper->setImageFile($product->getData(self::IMAGE_TYPE))->resize($_imageWidth, $_imageHeight)->getUrl();
            if ($imageUrl) {
                break;
            }
        }

        return $imageUrl;
    }

    protected function _getProductsFromCategory(\Magento\Catalog\Model\Category $category)
    {
        if ($category) {
            $productCollection = $category->getProductCollection();
            $productCollection->addAttributeToSelect('*');
            $productCollection->addAttributeToFilter('status', [ 'in' => $this->productStatus->getVisibleStatusIds() ]);
            $productCollection->addAttributeToFilter('visibility', [ 'in' => $this->productVisibility->getVisibleInSiteIds() ]);
            $productCollection->load();

            if ($productCollection) {
                return $this->mProductHelper->convertProductCollectionToResponseV2($productCollection);
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    protected function _getGalleryCategory()
    {
        //TODO: Remove hard data
        return [
            'https://images.livemint.com/rf/Image-621x414/LiveMint/Period2/2017/10/31/Photos/Processed/fruits-kFLF--621x414@LiveMint.jpg',
            'https://pbs.twimg.com/profile_images/996599426796797952/7vcDjxzu_400x400.jpg',
            'https://media.gettyimages.com/photos/assortment-of-fruits-picture-id173255460',
            'https://www.unlockfood.ca/EatRightOntario/media/Website-images-resized/How-to-store-fruit-to-keep-it-fresh-resized.jpg',
            'https://cached.imagescaler.hbpl.co.uk/resize/scaleWidth/815/cached.offlinehbpl.hbpl.co.uk/news/OMC/Pepsilogo02-201310210104514411-20180524105721574.jpg',
            'https://www.interbrand.com/assets/00000001313.png',
            'https://www.interbrand.com/assets/00000001551.png',
            'https://ih1.redbubble.net/image.1102551917.4147/flat,750x,075,f-pad,750x1000,f8f8f8.jpg',
        ];
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
