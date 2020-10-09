<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * TargetRule Catalog Product List Related Block
 *
 */
namespace SM\Product\Block\Catalog\Product\ProductList;

use Magento\TargetRule\Block\Catalog\Product\ProductList\Related as BlockRelatedCore;

/**
 * @api
 * @since 100.0.2
 */
class Related extends BlockRelatedCore
{
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * Related constructor.
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
     * Retrieve Catalog Product List Type identifier
     *
     * @return int
     */
    public function getProductListType()
    {
        return \Magento\TargetRule\Model\Rule::RELATED_PRODUCTS;
    }

    /**
     * Retrieve array of exclude product ids
     * Custom do not remove item related when that product added to cart
     *
     * @return array
     */
    public function getExcludeProductIds()
    {
        if ($this->_excludeProductIds === null) {
            $this->_excludeProductIds = [$this->getProduct()->getEntityId()];
        }
        return $this->_excludeProductIds;
    }

    /**
     * Find out if some products can be easy added to cart
     *
     * @return bool
     */
    public function canItemsAddToCart()
    {
        foreach ($this->getAllItems() as $item) {
            if (!$item->isComposite() && $item->isSaleable() && !$item->getRequiredOptions()) {
                return true;
            }
        }
        return false;
    }
}
