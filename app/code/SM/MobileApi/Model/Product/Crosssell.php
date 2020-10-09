<?php

namespace SM\MobileApi\Model\Product;

/**
 * Class Crosssell
 * @package SM\MobileApi\Model\Product
 */
class Crosssell
{
    protected $moduleManager;
    protected $japiProductHelper;
    protected $objectManager;

    /**
     * Crosssell constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \SM\MobileApi\Helper\Product $japiProductHelper
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \SM\MobileApi\Helper\Product $japiProductHelper
    ) {
        $this->moduleManager     = $moduleManager;
        $this->japiProductHelper = $japiProductHelper;
        $this->objectManager     = $objectManager;
    }

    /**
     * Get cart cross-sell products
     *
     * @return array
     */
    public function getList()
    {
        if ($this->moduleManager->isEnabled('Magento_TargetRule') && $this->moduleManager->isOutputEnabled('Magento_TargetRule')) {
            return $this->getEEList();
        } else {
            return $this->getCEList();
        }
    }

    /**
     * Get cross-sell products for EE version
     *
     * @return array|null
     */
    public function getEEList()
    {
        /** @var \Magento\TargetRule\Block\Checkout\Cart\Crosssell $crossSellBlock */
        $crossSellBlock = $this->objectManager->get('Magento\TargetRule\Block\Checkout\Cart\Crosssell');
        if (! $crossSellBlock->getQuote()->hasItems()) {
            return null;
        }

        $collection = $crossSellBlock->getItemCollection();

        $ids = [];
        foreach ($collection as $item) {
            $ids[] = $item->getId();
        }

        return $this->japiProductHelper->convertProductIdsToResponseV2($ids);
    }

    /**
     * Get cross-sell products for EE version
     *
     * @return array
     */
    public function getCEList()
    {
        /** @var \Magento\Checkout\Block\Cart\Crosssell $crossSellBlock */
        $crossSellBlock = $this->objectManager->get('Magento\Checkout\Block\Cart\Crosssell');
        if (! $crossSellBlock->getQuote()->hasItems()) {
            return null;
        }

        $collection = $crossSellBlock->getItems();

        $ids = [];
        foreach ($collection as $item) {
            $ids[] = $item->getId();
        }

        return $this->japiProductHelper->convertProductIdsToResponseV2($ids);
    }
}
