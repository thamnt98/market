<?php
/**
 * Class CatalogCategorySaveAfter
 * @package SM\Catalog\Observer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Catalog\Observer;

use SM\Catalog\Setup\Patch\Data\AddHideCTAttribute;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ProductSaveAfter implements ObserverInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \SM\Catalog\Model\CTAButton\Query
     */
    private $ctaQuery;

    /**
     * @var \Magento\Catalog\Model\Product\Action
     */
    private $productAction;

    /**
     * ProductSaveBefore constructor.
     * @param \SM\Catalog\Model\CTAButton\Query $ctaQuery
     * @param \Magento\Catalog\Model\Product\Action $productAction
     */
    public function __construct(
        \SM\Catalog\Model\CTAButton\Query $ctaQuery,
        \Magento\Catalog\Model\Product\Action $productAction
    ) {
        $this->ctaQuery = $ctaQuery;
        $this->productAction = $productAction;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /**
         * @var \Magento\Catalog\Model\Product $product
         */
        $product = $observer->getEvent()->getDataObject();
        $categoryIds = $product->getCategoryIds();
        $productId = $product->getId();
        $storeIds = [0];
        $valueIsNull = 0;

        foreach ($categoryIds as $categoryId) {
            foreach ($storeIds as $storeId) {
                $ctaValues = $this->ctaQuery->getCTAValues($categoryId, $storeId);
                if ($ctaValues) {
                    foreach ($ctaValues as $value) {
                        if ($value) {
                            $this->saveProductAttribute($productId, $storeId, $value);
                            $valueIsNull = 0;
                            break;
                        }

                        $valueIsNull = 1;
                    }

                    if ($valueIsNull) {
                        $this->saveProductAttribute($productId, $storeId, 0);
                    }
                }
            }
        }
    }

    /**
     * @param $productId
     * @param $storeId
     * @param $value
     */
    protected function saveProductAttribute($productId, $storeId, $value)
    {
        $this->productAction->updateAttributes(
            [$productId],
            array(
                AddHideCTAttribute::CTA_ATTRIBUTE =>
                    $value
            ),
            $storeId
        );
    }
}
