<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_GroupProduct
 *
 * Date: May, 14 2020
 * Time: 1:42 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\GroupProduct\Block\Product\View;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Locale\Format;

class Group extends \Magento\GroupedProduct\Block\Product\View\Type\Grouped
{
    protected $_template = 'product/view/grouped.phtml';

    protected $configurableBlock = [];

    /**
     * @var \Magento\Swatches\Block\Product\Renderer\ConfigurableFactory
     */
    protected $configurableBlockFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var null|float
     */
    protected $defaultTotalPrice = null;

    /**
     * @var \SM\Catalog\Block\Product\Delivery[]
     */
    protected $deliveryBlocks = [];

    /**
     * @var \SM\Catalog\Block\Product\StorePickup[]
     */
    protected $pickupBlocks = [];

    /**
     * @var \SM\Catalog\Helper\Data
     */
    protected $catalogHelper;

    /**
     * @var \SM\Catalog\Helper\StorePickup
     */
    protected $helperStorePickup;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\CatalogInventory\Model\StockRegistry
     */
    protected $stockRegistry;

    /**
     * @var Format
     */
    private $localeFormat;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * Group constructor.
     * @param \SM\Catalog\Helper\Data $catalogHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param \Magento\Swatches\Block\Product\Renderer\ConfigurableFactory $configurableBlockFactory
     * @param \SM\Catalog\Helper\StorePickup $helperStorePickup
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\CatalogInventory\Model\StockRegistry $stockRegistry
     * @param array $data
     */
    public function __construct(
        Format $localeFormat = null,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \SM\Catalog\Helper\Data $catalogHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Swatches\Block\Product\Renderer\ConfigurableFactory $configurableBlockFactory,
        \SM\Catalog\Helper\StorePickup $helperStorePickup,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\CatalogInventory\Model\StockRegistry $stockRegistry,
        array $data = []
    ) {
        parent::__construct($context, $arrayUtils, $data);
        $this->configurableBlockFactory = $configurableBlockFactory;
        $this->productRepository = $productRepository;
        $this->catalogHelper = $catalogHelper;
        $this->helperStorePickup = $helperStorePickup;
        $this->priceCurrency = $priceCurrency;
        $this->stockRegistry = $stockRegistry;
        $this->localeFormat = $localeFormat ?: ObjectManager::getInstance()->get(Format::class);
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     *  Get all child product available.
     *
     * @return \Magento\Catalog\Model\Product[]
     */
    public function getChildAvailable()
    {
        $result = [];
        $this->defaultTotalPrice = 0;
        /** @var \Magento\Catalog\Model\Product $associatedProduct */
        foreach ($this->getAssociatedProducts() as $associatedProduct) {
            try {
                $stockStatus = $this->stockRegistry->getProductStockStatus($associatedProduct->getId(), null);
                if ($associatedProduct->isSaleable() && $stockStatus) {
                    /** @var \Magento\Catalog\Model\Product $product */
                    $product = $this->productRepository->getById($associatedProduct->getId());
                    $product->setQty($associatedProduct->getQty());
                    $this->defaultTotalPrice += $product->getFinalPrice() * $product->getQty();
                    $result[] = $product;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return $result;
    }

    /**
     * @param $product
     * @return int|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getIsWarehouse($product, $ignore = true)
    {
        $count = 0;
        if ($product->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $stores = $this->helperStorePickup->getSourcesListConfigurable($product, $ignore);
        }

        if ($product->getTypeId() === \SM\Catalog\Helper\StorePickup::PRODUCT_SIMPLE) {
            $stores = $this->helperStorePickup->getSourcesListSimple($product, $ignore);
        }
        $count = (isset($stores)) ? count($stores) : $count;
        return $count;
    }

    /**
     * @return string
     */
    public function getDefaultTotalPriceTxt()
    {
        try {
            /** @var \Magento\Store\Model\Store $store */
            $store = $this->_storeManager->getStore();

            return $store->getCurrentCurrency()->format($this->getDefaultTotalPrice());
        } catch (\Exception $exception) {
            return $this->defaultTotalPrice;
        }
    }

    /**
     * @return float|null
     */
    public function getDefaultTotalPrice()
    {
        if (is_null($this->defaultTotalPrice)) {
            $this->getChildAvailable();
        }

        return $this->defaultTotalPrice;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getJsonConfig($product)
    {
        try {
            if ($product->getTypeId() === 'configurable') {
                $block = $this->createConfigurableBlock($product);

                return $block->getJsonConfig();
            }
        } catch (\Exception $e) {
        }

        return '{}';
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getJsonSwatchConfig($product)
    {
        try {
            if ($product->getTypeId() === 'configurable') {
                $block = $this->createConfigurableBlock($product);

                return $block->getJsonSwatchConfig();
            }
        } catch (\Exception $e) {
        }

        return '{}';
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return \Magento\Swatches\Block\Product\Renderer\Configurable
     * @throws \Exception
     */
    protected function createConfigurableBlock($product)
    {
        if (empty($this->configurableBlock[$product->getId()])) {
            /** @var \Magento\Swatches\Block\Product\Renderer\Configurable $block */
            $block = $this->configurableBlockFactory->create();
            try {
                $product = $this->productRepository->getById($product->getId());
            } catch (\Exception $e) {
                throw $e;
            }

            $block->setProduct($product);
            $this->configurableBlock[$product->getId()] = $block;
        }

        return $this->configurableBlock[$product->getId()];
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getDeliveryPopupHtml($product)
    {
        try {
            if (isset($this->deliveryBlocks[$product->getId()])) {
                $block = $this->deliveryBlocks[$product->getId()];
            } else {
                /** @var \SM\Catalog\Block\Product\Delivery $block */
                $block = $this->getLayout()->createBlock(\SM\Catalog\Block\Product\Delivery::class);
                $block->setProduct($product);
            }

            return $block->toHtml();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return '';
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getStorePickupPopupHtml($product)
    {
        try {
            if (isset($this->pickupBlocks[$product->getId()])) {
                $block = $this->pickupBlocks[$product->getId()];
            } else {
                /** @var \SM\Catalog\Block\Product\StorePickup $block */
                $block = $this->getLayout()->createBlock(\SM\Catalog\Block\Product\StorePickup::class);
                $block->setProduct($product);
            }

            return $block->toHtml();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return '';
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return int
     */
    public function getDefaultConfigurableId($product)
    {
        $minProduct = $this->catalogHelper->getMinConfigurable($product);
        if ($minProduct) {
            return $minProduct->getId();
        }

        return 0;
    }

    /**
     * @param $amount
     * @return string
     */
    public function formatPriceSimple($amount)
    {
        return $this->priceCurrency->convertAndFormat($amount);
    }

    public function getFormatPrice()
    {
        if ($this->localeFormat->getPriceFormat()) {
            $priceFormat = $this->localeFormat->getPriceFormat();
        } else {
            $priceFormat = [];
        }
        return $this->jsonEncoder->encode($priceFormat);
    }
}
