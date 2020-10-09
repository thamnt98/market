<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Model
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Eav\Model\AttributeRepository;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use SM\DigitalProduct\Api\Data\C1CategoryDataInterface;
use SM\DigitalProduct\Api\Data\C1CategoryDataInterfaceFactory;
use SM\DigitalProduct\Api\Data\CategoryInterface;
use SM\DigitalProduct\Api\Data\OperatorDataInterface;
use SM\DigitalProduct\Api\Data\OperatorDataInterfaceFactory;
use SM\DigitalProduct\Api\DigitalProductRepositoryInterface;
use SM\DigitalProduct\Helper\Category\Data;
use SM\DigitalProduct\Helper\Config;
use SM\DigitalProduct\Model\ResourceModel\Category\Collection as DigitalCatCollection;
use SM\DigitalProduct\Model\ResourceModel\Category\CollectionFactory as DigitalCatCollectionFactory;
use SM\HeroBanner\Model\Banner;
use Trans\DigitalProduct\Api\Data\DigitalProductOperatorListInterface;
use Trans\DigitalProduct\Model\DigitalProductGetOperator;
use Trans\DigitalProduct\Model\DigitalProductOperatorList;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductOperatorList\Collection as OperatorCollection;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductOperatorList\CollectionFactory as OperatorCollectionFactory;
use \SM\DigitalProduct\Api\Data\ProductInterfaceFactory as DigitalProductFactory;

/**
 * Class DigitalProductRepository
 * @package SM\DigitalProduct\Model
 */
class DigitalProductRepository implements DigitalProductRepositoryInterface
{
    /**
     * @var OperatorCollectionFactory
     */
    protected $operatorCollectionFactory;
    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var CategoryRepository
     */
    protected $digitalCategoryRepository;
    /**
     * @var C1CategoryDataInterfaceFactory
     */
    protected $c1CategoryDataFactory;

    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;
    /**
     * @var DigitalCatCollectionFactory
     */
    protected $digitalCatCollectionFactory;
    /**
     * @var OperatorDataInterfaceFactory
     */
    protected $operatorDataFactory;
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var Emulation
     */
    protected $emulation;

    /**
     * @var AttributeRepository
     */
    protected $attributeRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var DigitalProductGetOperator
     */
    protected $digitalGetOperator;

    /**
     * @var DigitalProductFactory
     */
    private $digitalProductFactory;

    /**
     * DigitalProductRepository constructor.
     * @param OperatorCollectionFactory $operatorCollectionFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param CategoryRepository $digitalCategoryRepository
     * @param C1CategoryDataInterfaceFactory $c1CategoryDataFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param CustomerRepository $customerRepository
     * @param DigitalCatCollectionFactory $digitalCatCollectionFactory
     * @param OperatorDataInterfaceFactory $operatorDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param Emulation $emulation
     * @param AttributeRepository $attributeRepository
     * @param Image $imageHelper
     * @param StoreManagerInterface $storeManager
     * @param DigitalProductGetOperator $digitalGetOperator
     * @param DigitalProductFactory $digitalProductFactory
     */
    public function __construct(
        OperatorCollectionFactory $operatorCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        CategoryRepository $digitalCategoryRepository,
        C1CategoryDataInterfaceFactory $c1CategoryDataFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        CustomerRepository $customerRepository,
        DigitalCatCollectionFactory $digitalCatCollectionFactory,
        OperatorDataInterfaceFactory $operatorDataFactory,
        DataObjectHelper $dataObjectHelper,
        Emulation $emulation,
        AttributeRepository $attributeRepository,
        Image $imageHelper,
        StoreManagerInterface $storeManager,
        DigitalProductGetOperator $digitalGetOperator,
        DigitalProductFactory $digitalProductFactory
    ) {
        $this->digitalGetOperator = $digitalGetOperator;
        $this->storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
        $this->attributeRepository = $attributeRepository;
        $this->emulation = $emulation;
        $this->operatorDataFactory = $operatorDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->digitalCatCollectionFactory = $digitalCatCollectionFactory;
        $this->customerRepository = $customerRepository;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->c1CategoryDataFactory = $c1CategoryDataFactory;
        $this->digitalCategoryRepository = $digitalCategoryRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->operatorCollectionFactory = $operatorCollectionFactory;
        $this->digitalProductFactory = $digitalProductFactory;
    }

    /**
     * @param string $number
     * @param $digitalCatCode
     * @return OperatorDataInterface
     * @throws LocalizedException
     */
    public function checkPrefix($number, $digitalCatCode = null)
    {
        $prefix = substr($number, 0, 4);
        /** @var OperatorCollection $operatorCollection */
        $operatorCollection = $this->operatorCollectionFactory->create();

        $operatorCollection->getSelect()->joinLeft(
            ["operator_icon" => "sm_digitalproduct_operator_icon"],
            "operator_icon.operator_service = main_table.brand_id"
        );
        $operatorCollection->addFieldToFilter(
            "main_table." . DigitalProductOperatorListInterface::PREFIX_NUMBER,
            $prefix
        );

        /** @var DigitalProductOperatorList $operator */
        $operator = $operatorCollection->getFirstItem();
        if ($operator->getId()) {

            /** @var OperatorDataInterface $operatorData */
            $operatorData = $this->operatorDataFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $operatorData,
                $operator->getData(),
                OperatorDataInterface::class
            );
            try {
                $storeId = $this->storeManager->getStore()->getId();
            } catch (NoSuchEntityException $e) {
                $storeId = 0;
            }

            $this->emulation->startEnvironmentEmulation(
                $storeId,
                Area::AREA_FRONTEND,
                true
            );
            $iconJson = $operator->getData("icon");
            $iconPart = is_null($iconJson) ? [] : json_decode($iconJson, true);
            if (is_array($iconPart) && isset($iconPart["url"])) {
                $operatorData->setOperatorIcon($iconPart["url"]);
            } else {
                $operatorData->setOperatorIcon($this->imageHelper->getDefaultPlaceholderUrl('image'));
            }

            if ($digitalCatCode) {
                $brandId = $operator->getBrandId();
                /**
                 * @todo Filter product by brandId
                 */
                $operatorData->setProducts($this->getProductsByCategoryMobile($digitalCatCode, $brandId = null));
            }
            $this->emulation->stopEnvironmentEmulation();
            return $operatorData;
        } else {
            try {
                $storeId = $this->storeManager->getStore()->getId();
            } catch (NoSuchEntityException $e) {
                $storeId = 0;
            }

            $this->emulation->startEnvironmentEmulation(
                $storeId,
                Area::AREA_FRONTEND,
                true
            );

            throw new LocalizedException(
                __("We cannot find this number. Make sure you enter the correct mobile number")
            );

            $this->emulation->stopEnvironmentEmulation();
        }
    }

    /**
     * @param string $digitalCatCode
     * @return array
     */
    private function categoryProcess($digitalCatCode)
    {
        /** @var DigitalCatCollection $digitalCatCollection */
        $digitalCatCollection = $this->digitalCatCollectionFactory->create();
        $digitalCatCollection->addFieldToFilter(
            CategoryInterface::TYPE,
            $digitalCatCode
        );

        /** @var Category $digitalCat */
        $digitalCat = $digitalCatCollection->getFirstItem();
        return [
            CategoryInterface::TYPE => $digitalCat->getData(CategoryInterface::TYPE),
            CategoryInterface::MAGENTO_CATEGORY_IDS => $digitalCat->getData(CategoryInterface::MAGENTO_CATEGORY_IDS)
        ];
    }

    /**
     * @param string $digitalCatCode
     * @param string $brandId
     * @return C1CategoryDataInterface
     */
    public function getProductsByCategory($digitalCatCode, $brandId = null)
    {
        $category = $this->categoryProcess($digitalCatCode);
        $categoryId = $category[CategoryInterface::MAGENTO_CATEGORY_IDS];
        $type = $category[CategoryInterface::TYPE];
        /** @var C1CategoryDataInterface $c1CategoryData */
        $c1CategoryData = $this->c1CategoryDataFactory->create();
        $productCollection = $this->getProductCollection($categoryId, $brandId);
        /** @var ProductInterface[] $products */
        $products = $productCollection->getItems();
        $c1CategoryData->setMagentoCategoryId($categoryId);
        $c1CategoryData->setType($type);
        $c1CategoryData->setProducts($products);
        return $c1CategoryData;
    }

    /**
     * @param string $digitalCatCode
     * @param null $brandId
     * @return array
     */
    public function getProductsByCategoryMobile($digitalCatCode, $brandId = null)
    {
        $category = $this->categoryProcess($digitalCatCode);
        $categoryId = $category[CategoryInterface::MAGENTO_CATEGORY_IDS];
        $productCollection = $this->getProductCollection($categoryId, $brandId);
        $products = array();

        foreach ($productCollection->getItems() as $item) {
            $product = $this->digitalProductFactory->create();

            $nameIsDemom = [Data::TOP_UP_VALUE, Data::ELECTRICITY_TOKEN_VALUE];
            if (in_array($digitalCatCode, $nameIsDemom)) {
                $item->setName(number_format((float)$item->getDenom(), '0', ',', '.'));
            }

            $item->setDescription(strip_tags($item->getDescription()));
            $catAllowGetProducts = array(Data::ELECTRICITY_TOKEN_VALUE);

            $item->setPrice($item->getPrice());
            if (!in_array($digitalCatCode, $catAllowGetProducts)) {
                $item->setSpecialPrice($item->getSpecialPrice());
            }
            $this->dataObjectHelper->populateWithArray(
                $product,
                $item->getData(),
                \SM\DigitalProduct\Api\Data\ProductInterface::class
            );
            $products[] = $product;
        }

        return $products;
    }


    /**
     * @param int $categoryId
     * @param null $brandId
     * @return ProductCollection
     */
    private function getProductCollection($categoryId, $brandId = null)
    {
        /** @var ProductCollection $productCollection */
        $productCollection = $this->productCollectionFactory->create()
            ->addAttributeToSelect(ProductInterface::PRICE)
            ->addAttributeToSelect(ProductInterface::NAME)
            ->addAttributeToSelect("description")
            ->addAttributeToSelect("denom")
            ->addAttributeToSelect("product_id_vendor")
            ->addAttributeToSelect("special_price");

        if (!is_null($brandId) && $brandId != "") {
            try {
                $this->attributeRepository->get("catalog_product", "brand_id_digital");
                $productCollection->addAttributeToFilter("brand_id_digital", $brandId);
            } catch (NoSuchEntityException $e) {
            }
        }

        $productCollection->addCategoriesFilter(["eq" => $categoryId])
            ->addWebsiteFilter();
        return $productCollection;
    }

    public function getOperator($productId)
    {
        return json_encode($this->digitalGetOperator->pdam($productId));
    }
}
