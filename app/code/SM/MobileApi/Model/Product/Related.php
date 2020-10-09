<?php

namespace SM\MobileApi\Model\Product;

/**
 * Class Related
 * @package SM\MobileApi\Model\Product
 */
class Related
{
    protected $productRepository;
    protected $moduleManager;
    protected $japiProductHelper;
    protected $objectManager;
    protected $coreRegistry;

    /**
     * Related constructor.
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
        $this->moduleManager     = $moduleManager;
        $this->japiProductHelper = $japiProductHelper;
        $this->objectManager     = $objectManager;
        $this->coreRegistry      = $coreRegistry;
    }

    /**
     * Get product related items
     *
     * @param $product_id
     *
     * @throws \Magento\Framework\Webapi\Exception
     * @return array
     */
    public function getList($product_id)
    {
        if (! $product_id) {
            throw new \Magento\Framework\Webapi\Exception(
                __('Product ID not found.'),
                0,
                \Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST
            );
        }

        $product = $this->productRepository->getById($product_id);
        if (! $product->getId()) {
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
        if ($this->moduleManager->isEnabled('Magento_TargetRule')) {
            return $this->getEEList();
        } else {
            return $this->getCEList();
        }
    }

    /**
     * Get product related collection from EE version
     *
     * @return array
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function getEEList()
    {
        /** @var \Magento\TargetRule\Block\Catalog\Product\ProductList\Related $relatedBlock */
        $relatedBlock = $this->objectManager->get('Magento\TargetRule\Block\Catalog\Product\ProductList\Related');
        $collection   = $relatedBlock->getAllItems();
        $limit        = (int) $relatedBlock->getPositionLimit();
        $limit        = $limit <= 0 ? 999 : $limit;

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
        /** @var \Magento\Catalog\Block\Product\ProductList\Related $relatedBlock */
        $relatedBlock = $this->objectManager->get('Magento\Catalog\Block\Product\ProductList\Related');
        $relatedBlock->toHtml();
        $collection = $relatedBlock->getItems();

        $ids = [];
        foreach ($collection as $item) {
            $ids[] = $item->getId();
        }

        return $this->japiProductHelper->convertProductIdsToResponseV2($ids);
    }
}
