<?php

/**
 * @category SM
 * @package SM_Brand
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author      Chinhvd <chinhvd@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Brand\Model;

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use SM\Brand\Api\BrandRepositoryInterface;
use Amasty\ShopbyBase\Api\Data\OptionSettingRepositoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use SM\MobileApi\Api\ProductInterface;
use SM\Brand\Api\Data\BrandInterface;
use SM\MobileApi\Helper\Product;
use SM\MobileApi\Model\Data\Product\LiistFactory;

class BrandRepository extends \Magento\Framework\View\Element\Template implements BrandRepositoryInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var OptionSettingRepositoryInterface
     */
    protected $optionSettingRepository;

    /**
     * @var ProductInterface
     */
    protected $productInterface;

    /**
     * @var BrandInterface
     */
    protected $brandInterface;

    /**
     * @var LiistFactory
     */
    protected $productListFactory;

    /**
     * @var Product
     */
    protected $productHelper;

    /**
     * @param Context $context
     * @param OptionSettingRepositoryInterface $optionSettingRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductRepository $productRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductInterface $productInterface
     * @param BrandInterface $brandInterface
     * @param Product $productHelper
     * @param LiistFactory $listFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        OptionSettingRepositoryInterface $optionSettingRepository,
        CategoryRepositoryInterface $categoryRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductRepository $productRepository,
        ScopeConfigInterface $scopeConfig,
        ProductInterface $productInterface,
        BrandInterface $brandInterface,
        Product $productHelper,
        LiistFactory $listFactory,
        array $data = []
    ) {
        $this->brandInterface = $brandInterface;
        $this->scopeConfig = $scopeConfig;
        $this->categoryRepository = $categoryRepository;
        $this->optionSettingRepository = $optionSettingRepository;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productInterface = $productInterface;
        $this->productListFactory  = $listFactory;
        $this->productHelper = $productHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param $brandId
     * @return \SM\MobileApi\Api\Data\Product\ListInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBrandProduct($brandId)
    {
        $data = [];
        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($this->getListProduct($brandId) as $product) {
            $data[] = $this->productHelper->getProductListToResponseV2($product);
        }
        /* @var $result \SM\MobileApi\Api\Data\Product\ListInterface */
        $result = $this->productListFactory->create();
        $result->setProducts($data);
        return $result;
    }

    /**
     * @param $brandId
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBrandCms($brandId)
    {
        $brandCms = $this->brandInterface
            ->setBanner($this->getBanner($brandId))
            ->setCategories($this->getCategories($brandId))
            ->setMostPopular($this->getMostPopular($brandId));
        return [$brandCms];
    }

    /**
     * @param $brandId
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListProduct($brandId)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $option = $this->optionSettingRepository->get($brandId);
        $value = $option->getValue();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('store_id', $storeId, 'in')
            ->addFilter($this->getBrandAttributeCode(), $value, 'in')
            ->create();
        return $this->productRepository->getList($searchCriteria)->getItems();
    }

    /**
     * @param $brandId
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCategories($brandId)
    {
        $categoryIds = [];
        $products = $this->getListProduct($brandId);
        foreach ($products as $product) {
            $categoryId = $product->getData('category_ids');
            $categoryIds = array_unique(array_merge($categoryIds, $categoryId));
        }
        $category = [];
        foreach ($categoryIds as $id) {
            $category[] = $this->categoryRepository->get($id);
        }
        return $category;
    }


    /**
     * @param $brandId
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMostPopular($brandId)
    {
        $option = $this->optionSettingRepository->get($brandId);
        $categoryId = $option->getMostPopularCategoryId();
        if (!$categoryId) {
            return [];
        }
        return $this->productInterface->getList($categoryId);
    }

    /**
     * @param $brandId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBanner($brandId)
    {
        $option = $this->optionSettingRepository->get($brandId);
        return $option->getImageUrl();
    }

    /**
     * @return string
     */
    protected function getBrandAttributeCode()
    {
        return $this->scopeConfig->getValue(
            'amshopby_brand/general/attribute_code',
            ScopeInterface::SCOPE_STORE
        );
    }
}
