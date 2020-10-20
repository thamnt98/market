<?php
/**
 * Class CategorySaveAfter
 * @package SM\Catalog\Observer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Catalog\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use SM\Catalog\Setup\Patch\Data\AddHideCTAttribute;

class CategorySaveAfter implements ObserverInterface
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
     * @var \SM\Catalog\Model\CTAButton\Query
     */
    private $ctaQuery;

    /**
     * CategorySaveBefore constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\Product\Action $productAction
     * @param \SM\Catalog\Model\CTAButton\Query $ctaQuery
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\Action $productAction,
        \SM\Catalog\Model\CTAButton\Query $ctaQuery
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->productAction = $productAction;
        $this->ctaQuery = $ctaQuery;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /**
         * @var \Magento\Catalog\Model\Category $category
         */
        $category = $observer->getEvent()->getDataObject();
        if ($category->dataHasChangedFor(AddHideCTAttribute::CTA_ATTRIBUTE) &&
            !$this->scopeConfig->isSetFlag('trans_catalog/product/update_cta_cron')
            ) {
            $productIds = array_keys($category->getProductsPosition());
            if ($productIds) {
                $this->saveProductAttribute($category, $productIds);
            }
        }
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

        $this->productAction->updateAttributes(
            $productIds,
            array(
                AddHideCTAttribute::CTA_ATTRIBUTE => $category->getData(AddHideCTAttribute::CTA_ATTRIBUTE)
            ),
            $storeId
        );
    }
}
