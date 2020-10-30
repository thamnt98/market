<?php

namespace SM\MobileApi\Model;

/**
 * Class Product
 * @package SM\MobileApi\Model
 */
class Product implements \SM\MobileApi\Api\ProductInterface
{
    protected $productListFactory;
    protected $catalogCategory;
    protected $productFactory;
    protected $catalogSearch;
    protected $productRelated;
    protected $productUpsell;
    protected $productCrosssell;
    protected $productMulti;
    protected $productUrl;
    protected $helperData;
    protected $request;
    protected $smartProductRepository;
    protected $_registry;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \SM\MobileApi\Model\Data\Product\LiistFactory $listFactory,
        \SM\Category\Model\Catalog\Category $catalogCategory,
        \SM\MobileApi\Model\Data\Product\ProductFactory $productFactory,
        \SM\Category\Model\Catalog\Search $catalogSearch,
        \SM\MobileApi\Model\Product\Related $productRelated,
        \SM\MobileApi\Model\Product\Upsell $productUpsell,
        \SM\MobileApi\Model\Product\Crosssell $productCrosssell,
        \SM\MobileApi\Model\Product\Multi $productMulti,
        \SM\MobileApi\Model\Product\Url $productUrl,
        \SM\MobileApi\Helper\Data $helperData,
        \SM\Product\Api\Repository\ProductRepositoryInterface $smartProductRepository,
        \Magento\Framework\Registry $registry
    ) {
        $this->request                = $request;
        $this->productListFactory     = $listFactory;
        $this->catalogCategory        = $catalogCategory;
        $this->catalogSearch          = $catalogSearch;
        $this->productFactory         = $productFactory;
        $this->productRelated         = $productRelated;
        $this->productUpsell          = $productUpsell;
        $this->productCrosssell       = $productCrosssell;
        $this->productMulti           = $productMulti;
        $this->productUrl             = $productUrl;
        $this->helperData             = $helperData;
        $this->smartProductRepository = $smartProductRepository;
        $this->_registry = $registry;
    }

    /**
     * @param int $category_id
     * @param int $limit
     * @param int $p
     * @return \SM\MobileApi\Api\Data\Product\ListInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList($category_id, $limit = 12, $p = 1)
    {
        $this->catalogCategory->init($category_id);

        /* @var $result \SM\MobileApi\Api\Data\Product\ListInterface */
        $result = $this->productListFactory->create();
        $result->setCategoryId($category_id);
        $result->setFilters($this->catalogCategory->getFilters());
        $result->setToolbarInfo($this->catalogCategory->getToolbarInfo());
        $result->setProducts($this->catalogCategory->getProductsV2());

        return $result;
    }

    /**
     * @param int $product_id
     * @return \SM\MobileApi\Api\Data\Product\ProductInterface
     */
    public function getDetails($product_id)
    {
        /* @var \SM\MobileApi\Api\Data\Product\ProductInterface $result */
        $result = $this->productFactory->create();
        $result->setProduct($this->catalogCategory->getProductV2($product_id));

        return $result;
    }

    /**
     * @param string $sku
     * @param int $customerId
     * @return \SM\MobileApi\Api\Data\Product\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function getDetailsBySKU($sku, $customerId)
    {
        //Using customer id for calculate distance between customer and store
        $this->_registry->register('customer_id', $customerId);

        /* @var \SM\MobileApi\Api\Data\Product\ProductInterface $result */
        $result = $this->productFactory->create();
        $result->setProduct($this->catalogCategory->getProductV2BySKU($sku));

        //Using function to save last viewed product of customer
        $this->smartProductRepository->get($customerId, $sku);

        return $result;
    }

    /**
     * @param int $product_id
     * @return \SM\MobileApi\Api\Data\Product\ListInterface|\SM\MobileApi\Model\Data\Product\Liist
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function getRelatedProducts($product_id)
    {
        $result = $this->productListFactory->create();
        $result->setProducts($this->productRelated->getList($product_id));

        return $result;
    }
}
