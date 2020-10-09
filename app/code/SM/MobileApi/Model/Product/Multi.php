<?php

namespace SM\MobileApi\Model\Product;

/**
 * Class Multi
 * @package SM\MobileApi\Model\Product
 */
class Multi
{
    protected $productCollectionFactory;
    protected $storeManager;
    protected $catalogConfig;
    protected $catalogProductVisibility;
    protected $japiProductHelper;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \SM\MobileApi\Helper\Product $japiProductHelper
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
        $this->catalogConfig = $catalogConfig;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->japiProductHelper = $japiProductHelper;
    }

    /**
     * @param array $productTds
     * @return \SM\MobileApi\Api\Data\Product\ListItemInterface[]
     */
    public function getList($productTds)
    {
        $collection = $this->_getCollection();
        $collection->addIdFilter($productTds);

        return $this->japiProductHelper->convertProductCollectionToResponseV2($collection);
    }

    /**
     * Get product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getCollection()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->productCollectionFactory->create()
            ->setStoreId($this->storeManager->getStore()->getId())
            ->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());

        $this->_addProductAttributesAndPrices($collection);

        return $collection;
    }

    /**
     * Add all attributes and apply pricing logic to products collection
     * to get correct values in different products lists.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _addProductAttributesAndPrices(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    ) {
        return $collection
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addUrlRewrite();
    }
}
