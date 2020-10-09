<?php

namespace SM\MobileApi\Model\Product;

/**
 * Class Upsell
 * @package SM\MobileApi\Model\Product
 */
class Upsell
{
    protected $productRepository;
    protected $moduleManager;
    protected $japiProductHelper;
    protected $objectManager;
    protected $coreRegistry;

    /**
     * Upsell constructor.
     *
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \SM\MobileApi\Helper\Product $japiProductHelper
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \SM\MobileApi\Helper\Product $japiProductHelper
    ) {
        $this->productRepository = $productRepository;
        $this->moduleManager = $moduleManager;
        $this->japiProductHelper = $japiProductHelper;
        $this->objectManager = $objectManager;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Get product upsell items
     *
     * @param $product_id
     * @throws \Magento\Framework\Webapi\Exception
     * @return array
     */
    public function getList($product_id)
    {
        if (!$product_id) {
            throw new \Magento\Framework\Webapi\Exception(
                __('Product ID not found.'),
                0,
                \Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST
            );
        }

        $product = $this->productRepository->getById($product_id);
        if (!$product->getId()) {
            throw new \Magento\Framework\Webapi\Exception(
                __('Product not found.'),
                0,
                \Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR
            );
        }

        /**
         * Register product to registry for later use
         */
        $this->coreRegistry->register('product', $product, true);

        /**
         * Switch between CE & EE version
         */
        if ($this->moduleManager->isEnabled('Magento_TargetRule') && $this->moduleManager->isOutputEnabled('Magento_TargetRule')) {
            return $this->getEEList();
        } else {
            return $this->getCEList();
        }
    }

    /**
     * Get product upsell collection from EE version
     *
     * @return array
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function getEEList()
    {
        /** @var \Magento\TargetRule\Block\Catalog\Product\ProductList\Upsell $upsellBlock */
        $upsellBlock = $this->objectManager->get('Magento\TargetRule\Block\Catalog\Product\ProductList\Upsell');
        $collection = $upsellBlock->getAllItems();
        $limit = (int)$upsellBlock->getPositionLimit();
        $limit = $limit <= 0 ? 999 : $limit;

        $ids = [];
        foreach ($collection as $item) {
            if (count($ids) >= $limit) {
                break;
            }
            $ids[] = $item->getId();
        }

        return $this->japiProductHelper->convertProductIdsToResponseV2($ids);
    }

    /**
     * Get product upsell collection from CE version
     *
     * @return array
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function getCEList()
    {
        /** @var \Magento\Catalog\Block\Product\ProductList\Upsell $upsellBlock */
        $upsellBlock = $this->objectManager->get('Magento\Catalog\Block\Product\ProductList\Upsell');
        $upsellBlock->toHtml();
        $collection = $upsellBlock->getItemCollection();
        $limit = (int)$upsellBlock->getItemLimit('upsell');
        $limit = $limit <= 0 ? 999 : $limit;

        $ids = [];
        foreach ($collection as $item) {
            if (count($ids) >= $limit) {
                break;
            }
            $ids[] = $item->getId();
        }

        return $this->japiProductHelper->convertProductIdsToResponseV2($ids);
    }
}
