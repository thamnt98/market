<?php
/**
 * Class UpdateCTAButton
 * @package SM\Catalog\Cron\Product
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Catalog\Cron\Product;

use Magento\Framework\Exception\LocalizedException;
use SM\Catalog\Setup\Patch\Data\AddHideCTAttribute;

class UpdateCTAButton
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Catalog\Model\Product\Action
     */
    private $productAction;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category
     */
    private $categoryResource;

    /**
     * @var \SM\Catalog\Model\CTAButton\Query
     */
    private $ctaQuery;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    private $categoryCollection;

    /**
     * CategorySaveBefore constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\Product\Action $productAction
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category $categoryResource
     * @param \SM\Catalog\Model\CTAButton\Query $ctaQuery
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\Action $productAction,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category $categoryResource,
        \SM\Catalog\Model\CTAButton\Query $ctaQuery,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->productAction = $productAction;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryResource = $categoryResource;
        $this->ctaQuery = $ctaQuery;
        $this->logger = $logger;
    }

    /**
     * @return bool
     */
    public function execute()
    {
        if ($this->scopeConfig->isSetFlag('trans_catalog/product/update_cta_cron')) {
            $this->categoryCollection = $this->categoryCollectionFactory->create();
            try {
                $this->categoryCollection->addAttributeToFilter('is_changed_cta', '1')
                    ->addAttributeToSelect(AddHideCTAttribute::CTA_ATTRIBUTE);
            } catch (LocalizedException $e) {
                $this->logger->error(get_class($this) . ":" . $e->getMessage());
                return false;
            }

            if ($this->categoryCollection->getSize()) {
                return $this->updateAttribute();
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function updateAttribute()
    {
        /**
         * @var $category \Magento\Catalog\Model\Category
         */
        foreach ($this->categoryCollection as $category) {
            $productIds = array_keys($category->getProductsPosition());
            if ($productIds) {
                try {
                    $this->saveProductAttribute($category, $productIds);
                    $this->categoryResource->saveAttribute($category, 'is_changed_cta');
                } catch (\Exception $e) {
                    $this->logger->error(get_class($this) . ":" . $e->getMessage());
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param $category
     * @param $productIds
     */
    protected function saveProductAttribute($category, $productIds)
    {
        $currentCategoryId = $category->getId();
        $storeId = 0;
        $this->ctaQuery->getProductsFilter($storeId, $currentCategoryId, $productIds);
        if ($productIds) {
            $ctaValues = $this->ctaQuery->getCTAValues($category->getId(), $storeId);
            if ($ctaValues) {
                foreach ($ctaValues as $value) {
                    $this->productAction->updateAttributes(
                        $productIds,
                        array(
                            AddHideCTAttribute::CTA_ATTRIBUTE =>
                                $value
                        ),
                        $storeId
                    );
                }
            }
        }
        $category->setData('is_changed_cta', '0');
    }
}
