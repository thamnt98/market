<?php

namespace SM\Checkout\Plugin\CustomerData;

use Magento\Checkout\CustomerData\ItemPoolInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Cart extends \Magento\Checkout\CustomerData\Cart
{
    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        \Magento\Checkout\Model\Cart $checkoutCart,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        ItemPoolInterface $itemPoolInterface,
        \Magento\Framework\View\LayoutInterface $layout,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($checkoutSession, $catalogUrl, $checkoutCart, $checkoutHelper, $itemPoolInterface, $layout, $data);
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Checkout\CustomerData\Cart $subject
     * @return array
     * @throws \Exception
     */
    public function beforeGetSectionData(
        \Magento\Checkout\CustomerData\Cart $subject
    ) {
        $quote = $subject->getQuote();
        if ($quote->getIsVirtual() && !$this->checkoutSession->getDigital()) {
            foreach ($quote->getItemsCollection() as $item) {
                if (!$item->getIsVirtual() && $item->getIsActive() == 0) {
                    $item->setIsActive(1);
                } elseif ($item->getIsVirtual() && $item->getIsActive() == 1) {
                    $item->setIsActive(0);
                }
            }
            $quote->collectTotals()->save();
        }
        $this->checkoutSession->unsDigital();
        return [];
    }

    /**
     * @param \Magento\Checkout\CustomerData\Cart $subject
     * @param array $sectionData
     * @return array
     */
    public function afterGetSectionData(\Magento\Checkout\CustomerData\Cart $subject, $sectionData)
    {
        $quote = $subject->getQuote();
        $itemCount = 0;
        $itemQty = 0;
        foreach ($quote->getItemsCollection() as $item) {
            if ($item->getIsVirtual()) {
                continue;
            }
            $itemCount ++;
            $itemQty += $item->getQty();
        }
        $useQty = $this->scopeConfig->getValue(
            'checkout/cart_link/use_qty',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $summaryQty = $useQty ? $itemQty : $itemCount;
        $sectionData['summary_count'] = $summaryQty;
        return $sectionData;
    }
}
