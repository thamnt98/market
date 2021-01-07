<?php

namespace SM\MobileApi\Model;

/**
 * Class Product
 *
 * @package SM\MobileApi\Model
 */
class Product implements \SM\MobileApi\Api\ProductInterface
{
    /**
     * @var Data\Product\LiistFactory
     */
    protected $productListFactory;

    /**
     * @var \SM\Category\Model\Catalog\Category
     */
    protected $catalogCategory;

    /**
     * @var Data\Product\ProductFactory
     */
    protected $productFactory;

    /**
     * @var Product\Related
     */
    protected $productRelated;

    /**
     * @var \SM\Product\Api\Repository\ProductRepositoryInterface
     */
    protected $smartProductRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    protected $tokenUserContext;

    /**
     * Product constructor.
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param Data\Product\LiistFactory $listFactory
     * @param \SM\Category\Model\Catalog\Category $catalogCategory
     * @param Data\Product\ProductFactory $productFactory
     * @param Product\Related $productRelated
     * @param \SM\Product\Api\Repository\ProductRepositoryInterface $smartProductRepository
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Authorization\Model\UserContextInterface $tokenUserContext
     */
    public function __construct(
        \Magento\Customer\Model\Session $session,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \SM\MobileApi\Model\Data\Product\LiistFactory $listFactory,
        \SM\Category\Model\Catalog\Category $catalogCategory,
        \SM\MobileApi\Model\Data\Product\ProductFactory $productFactory,
        \SM\MobileApi\Model\Product\Related $productRelated,
        \SM\Product\Api\Repository\ProductRepositoryInterface $smartProductRepository,
        \Magento\Framework\Registry $registry,
        \Magento\Authorization\Model\UserContextInterface $tokenUserContext
    ) {
        $this->productListFactory     = $listFactory;
        $this->catalogCategory        = $catalogCategory;
        $this->productFactory         = $productFactory;
        $this->productRelated         = $productRelated;
        $this->smartProductRepository = $smartProductRepository;
        $this->registry               = $registry;
        $this->customerFactory        = $customerFactory;
        $this->session                = $session;
        $this->tokenUserContext       = $tokenUserContext;
    }

    /**
     * @param int $category_id
     * @param int $limit
     * @param int $p
     * @param bool $layer
     * @param int $customerId
     * @return \SM\MobileApi\Api\Data\Product\ListInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList($category_id, $limit = 12, $p = 1, $layer = true, $customerId = 0)
    {
        $this->initCustomerSession($customerId);
        //Init category and apply filter
        $this->catalogCategory->init($category_id);
        $result = $this->productListFactory->create();

        //Init toolbar and apply pagination
        $toolbar = $this->catalogCategory->getToolbarInfo();
        $result->setToolbarInfo($toolbar);

        /* @var $result \SM\MobileApi\Api\Data\Product\ListInterface */
        $result->setCategoryId($category_id);
        if ($layer) {
            $this->catalogCategory->applyFilter();
            $result->setFilters($this->catalogCategory->getFilters());

        }

        $result->setProducts($this->catalogCategory->getProductsV2());

        return $result;
    }

    /**
     * @param int $product_id
     * @return \SM\MobileApi\Api\Data\Product\ProductInterface
     * @throws \Magento\Framework\Webapi\Exception
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
    public function getDetailsBySKU($sku)
    {
        $customerId = $this->tokenUserContext->getUserId();
        if ($customerId != 0) {
            //Using customer id for calculate distance between customer and store
            $this->registry->register('customer_id', $customerId);
        }
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

    /**
     * @param int $customerId
     */
    protected function initCustomerSession($customerId)
    {
        if ($this->session->isLoggedIn() || !$customerId || $customerId == 0) {
            return;
        }

        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->customerFactory->create()->load($customerId);
        if ($customer && $customer->getId()) {
            $this->session->setCustomerAsLoggedIn($customer);
        }
    }
}
