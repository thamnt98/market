<?php

namespace SM\Checkout\Plugin\CustomerData;

class Cart extends \Magento\Checkout\CustomerData\Cart
{
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
}
