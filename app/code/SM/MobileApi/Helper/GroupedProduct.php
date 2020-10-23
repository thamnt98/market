<?php
namespace SM\MobileApi\Helper;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\GroupedProduct\Model\Product\Type\Grouped;

/**
 * Class GroupedProduct
 *
 * @package SM\MobileApi\Helper
 */
class GroupedProduct
{
    /**
     * @var \SM\MobileApi\Model\Data\Catalog\Product\Grouped\ProductItemsFactory
     */
    protected $productItemsFactory;

    /**
     * @var \SM\MobileApi\Model\Product\Image
     */
    protected $_productImageHelper;

    /**
     * @var Configurable
     */
    protected $_configurableHelper;

    /**
     * @var Product\Common
     */
    protected $_commonProductHelper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var \SM\MobileApi\Model\Product\Stock
     */
    protected $_productStock;

    /**
     * @var Price
     */
    protected $_priceHelper;

    /**
     * @var \SM\GTM\Block\Product\ListProduct
     */
    protected $productGtm;

    /**
     * @var \SM\MobileApi\Model\Data\GTM\GTMFactory
     */
    protected $gtmDataFactory;

    /**
     * GroupedProduct constructor.
     * @param \SM\MobileApi\Model\Data\Catalog\Product\Grouped\ProductItemsFactory $productItemsFactory
     * @param \SM\MobileApi\Model\Product\Image $productHelperImage
     * @param Configurable $_configurableHelper
     * @param Product\Common $_commonProductHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \SM\MobileApi\Model\Product\Stock $productStock
     * @param Price $priceHelper
     * @param \SM\GTM\Block\Product\ListProduct $productGtm
     * @param \SM\MobileApi\Model\Data\GTM\GTMFactory $gtmDataFactory
     */
    public function __construct(
        \SM\MobileApi\Model\Data\Catalog\Product\Grouped\ProductItemsFactory $productItemsFactory,
        \SM\MobileApi\Model\Product\Image $productHelperImage,
        Configurable $_configurableHelper,
        \SM\MobileApi\Helper\Product\Common $_commonProductHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \SM\MobileApi\Model\Product\Stock $productStock,
        Price $priceHelper,
        \SM\GTM\Block\Product\ListProduct $productGtm,
        \SM\MobileApi\Model\Data\GTM\GTMFactory $gtmDataFactory
    ) {
        $this->productItemsFactory = $productItemsFactory;
        $this->_productImageHelper = $productHelperImage;
        $this->_configurableHelper = $_configurableHelper;
        $this->_commonProductHelper = $_commonProductHelper;
        $this->_productRepository = $productRepository;
        $this->_productStock = $productStock;
        $this->_priceHelper = $priceHelper;
        $this->gtmDataFactory = $gtmDataFactory;
        $this->productGtm = $productGtm;
    }

    /**
     * @param $product
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getGroupedItems($product)
    {
        $childProducts = $product->getTypeInstance()->getAssociatedProducts($product);
        $result = [];

        if ($product->getTypeId() != Grouped::TYPE_CODE) {
            return [];
        }

        if (count($childProducts) == 0) {
            return [];
        }

        foreach ($childProducts as $item) {
            //Load product to get more information
            $productRepo = $this->_productRepository->getById($item->getEntityId());
            $finalPrice = $productRepo->getPriceInfo()->getPrice(\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE);
            $regularPrice = $productRepo->getPriceInfo()->getPrices()
                ->get(\Magento\Catalog\Pricing\Price\RegularPrice::PRICE_CODE)->getAmount()->getValue();

            $groupedProduct = $this->productItemsFactory->create();
            $groupedProduct->setId($productRepo->getId());
            $groupedProduct->setSku($productRepo->getSku());
            $groupedProduct->setStock($this->_productStock->getStock($productRepo));
            $groupedProduct->setImage($this->_productImageHelper->getMainImage($productRepo));
            $groupedProduct->setName($productRepo->getName());
            $groupedProduct->setType($productRepo->getTypeId());
            $groupedProduct->setProductLabel($this->_commonProductHelper->getProductLabel($productRepo));
            $groupedProduct->setQtyDefault($item->getQty());
            $groupedProduct->setPosition($item->getPosition());
            $groupedProduct->setIsAvailable($productRepo->isAvailable());
            $groupedProduct->setIsSaleable($this->_productStock->isProductSalable($productRepo));
            $groupedProduct->setDeliveryInto($this->_commonProductHelper->getDeliveryMethodProduct($item));
            $groupedProduct->setStoresInfo($this->_commonProductHelper->getStoreInfo($item));
            $groupedProduct->setFinalPrice($this->_priceHelper->formatPrice($finalPrice->getAmount()->getValue()));
            $groupedProduct->setPrice($this->_priceHelper->formatPrice($regularPrice));

            if ($item->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $groupedProduct->setConfigurableAttributes($this->_configurableHelper->getConfigurableAttributes($item));
            }

            $gtmData = $this->getGTMData($productRepo);
            $groupedProduct->setGtmData($gtmData);

            $result[] = $groupedProduct;
        }

        return $result;
    }

    public function getGTMData($product)
    {
        $data = $this->productGtm->getGtm($product);
        $data = \Zend_Json_Decoder::decode($data);
        /** @var \SM\MobileApi\Api\Data\GTM\GTMInterface $gtmData */
        $gtmData = $this->gtmDataFactory->create();
        $gtmData
            ->setProductName($data['name'])
            ->setProductId($data['id'])
            ->setProductPrice($data['price'])
            ->setProductBrand($data['brand'])
            ->setProductCategory($data['category'])
            ->setProductSize($data['product_size'])
            ->setProductVolume($data['product_volume'])
            ->setProductWeight($data['product_weight'])
            ->setProductVariant($data['variant'])
            ->setDiscountPrice($data['salePrice'])
            ->setProductList($data['list'])
            ->setProductRating($data['rating'])
            ->setInitialPrice($data['initialPrice'])
            ->setDiscountRate($data['discountRate'])
            ->setProductType($product->getTypeId());
        if ($data['salePrice'] && $data['salePrice'] > 0) {
            $gtmData->setProductOnSale(__('Yes'));
        } else {
            $gtmData->setProductOnSale(__('Not on sale'));
        }

        return $gtmData;
    }
}
