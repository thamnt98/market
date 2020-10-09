<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * TargetRule Catalog Product List Upsell Block
 *
 */
namespace SM\Product\Block\Catalog\Product\ProductList;

use Magento\TargetRule\Block\Catalog\Product\ProductList\Upsell as BlockUpsellCore;

/**
 * @api
 * @since 100.0.2
 */
class Upsell extends BlockUpsellCore
{
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\TargetRule\Model\ResourceModel\Index $index
     * @param \Magento\TargetRule\Helper\Data $targetRuleData
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\TargetRule\Model\IndexFactory $indexFactory
     * @param \Magento\Checkout\Model\Cart $cart
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\TargetRule\Model\ResourceModel\Index $index,
        \Magento\TargetRule\Helper\Data $targetRuleData,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\TargetRule\Model\IndexFactory $indexFactory,
        \Magento\Checkout\Model\Cart $cart,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $index,
            $targetRuleData,
            $productCollectionFactory,
            $visibility,
            $indexFactory,
            $cart,
            $data
        );
    }

    /**
     * Get all items
     *
     * @return array
     */
    public function getAllItems()
    {
        $collection = parent::getAllItems();
        $collectionMock = new \Magento\Framework\DataObject(['items' => $collection]);
        $this->_eventManager->dispatch(
            'catalog_product_upsell',
            [
                'product'       => $this->getProduct(),
                'collection'    => $collectionMock,
                'limit'         => null
            ]
        );

        //because follow design requirement only show related product
        $upsellProductSet = $this->getProduct()->getUpSellProducts();
        if (empty($upsellProductSet)) {
            return [];
        } else {
            return $collectionMock->getItems();
        }
    }
}
