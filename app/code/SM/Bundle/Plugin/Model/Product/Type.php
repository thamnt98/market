<?php
/**
 * Class Type
 * @package SM\Bundle\Plugin\Model\Product
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Bundle\Plugin\Model\Product;

use Magento\Bundle\Model\ResourceModel\Selection\Collection\FilterApplier as SelectionCollectionFilterApplier;
use Magento\Bundle\Model\ResourceModel\Selection\CollectionFactory as BundleSelectionCollection;
use Magento\Framework\EntityManager\MetadataPool;

class Type
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var SelectionCollectionFilterApplier
     */
    private $selectionCollectionFilterApplier;

    /**
     * @var BundleSelectionCollection
     */
    protected $bundleCollection;

    /**
     * Type constructor.
     * @param MetadataPool $metadataPool
     * @param SelectionCollectionFilterApplier $selectionCollectionFilterApplier
     * @param BundleSelectionCollection $bundleCollection
     */
    public function __construct(
        MetadataPool $metadataPool,
        SelectionCollectionFilterApplier $selectionCollectionFilterApplier,
        BundleSelectionCollection $bundleCollection
    ) {
        $this->metadataPool = $metadataPool;
        $this->selectionCollectionFilterApplier = $selectionCollectionFilterApplier;
        $this->bundleCollection = $bundleCollection;
    }

    /**
     * @param \Wizkunde\ConfigurableBundle\Model\Product\Type $subject
     * @param $product
     * @param callable $proceed
     * @return bool
     * @throws \Exception
     */
    public function aroundIsSalable(
        \Wizkunde\ConfigurableBundle\Model\Product\Type $subject,
        callable $proceed,
        $product
    ) {
        /**
         * @todo check bundle stock status
         * Disable this method for now
         */
        if ($salable = $this->getDefaultSalable($product)) {
            return $salable;
        }

        if ($product->hasData('all_items_salable')) {
            return $product->getData('all_items_salable');
        }

        $metadata = $this->metadataPool->getMetadata(
            \Magento\Catalog\Api\Data\ProductInterface::class
        );

        $isSalable = false;
        foreach ($subject->getOptionsCollection($product) as $option) {
            $hasSalable = false;

            $selectionsCollection = $this->bundleCollection->create();
            $selectionsCollection->addAttributeToSelect('status');
            $selectionsCollection->addQuantityFilter();
            $selectionsCollection->setFlag('product_children', true);
            $selectionsCollection->addFilterByRequiredOptions();
            $selectionsCollection->setOptionIdsFilter([$option->getId()]);

            $this->selectionCollectionFilterApplier->apply(
                $selectionsCollection,
                'parent_product_id',
                $product->getData($metadata->getLinkField())
            );

            foreach ($selectionsCollection as $selection) {
                if ($selection->isSalable()) {
                    $hasSalable = true;
                    break;
                }
            }

            if ($hasSalable) {
                $isSalable = true;
            }

            if (!$hasSalable && $option->getRequired()) {
                $isSalable = false;
                break;
            }
        }

        $product->setData('all_items_salable', $isSalable);

        return $isSalable;
    }

    /**
     * @param $product
     * @return bool
     */
    private function getDefaultSalable($product)
    {
        $salable = $product->getStatus() == \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED;
        if ($salable && $product->hasData('is_salable')) {
            $salable = $product->getData('is_salable');
        }

        return (bool)(int)$salable;
    }
}
