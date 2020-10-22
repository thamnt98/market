<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Wizkunde\ConfigurableBundle\Block\Adminhtml\Order\Create\Items;

/**
 * Adminhtml sales order create items grid block
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Grid extends \Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid
{
    protected $productConfiguration;
    protected $bundleProductConfiguration;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\GiftMessage\Model\Save $giftMessageSave
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\GiftMessage\Helper\Message $messageHelper
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockStateInterface $stockState
     * @param array $data
     * @param \Magento\Catalog\Helper\Product\Configuration $productConfiguration
     * @param \Magento\Bundle\Helper\Catalog\Product\Configuration $bundleProductConfiguration
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\GiftMessage\Model\Save $giftMessageSave,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\GiftMessage\Helper\Message $messageHelper,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        array $data = [],
        \Magento\Catalog\Helper\Product\Configuration $productConfiguration,
        \Magento\Bundle\Helper\Catalog\Product\Configuration $bundleProductConfiguration
    ) {
        parent::__construct(
            $context,
            $sessionQuote,
            $orderCreate,
            $priceCurrency,
            $wishlistFactory,
            $giftMessageSave,
            $taxConfig,
            $taxData,
            $messageHelper,
            $stockRegistry,
            $stockState,
            $data
        );

        $this->productConfiguration = $productConfiguration;
        $this->bundleProductConfiguration = $bundleProductConfiguration;
    }

    public function getFormatedOptionValue($optionValue)
    {
        $params = [
            'max_length' => 55,
            'cut_replacer' => ' <a href="#" class="dots tooltip toggle" onclick="return false">...</a>'
        ];
        return $this->productConfiguration->getFormattedOptionValue($optionValue, $params);
    }
    public function getOptionList($item)
    {
         return $this->bundleProductConfiguration->getOptions($item);
    }
}
