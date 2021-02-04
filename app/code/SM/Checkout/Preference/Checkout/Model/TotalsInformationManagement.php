<?php

namespace SM\Checkout\Preference\Checkout\Model;

class TotalsInformationManagement extends \Magento\Checkout\Model\TotalsInformationManagement
{
    /**
     * {@inheritDoc}
     */
    public function calculate(
        $cartId,
        \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
    ) {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->cartRepository->get($cartId);
        $this->validateQuote($quote);

        if ($quote->getIsVirtual()) {
            $quote->setBillingAddress($addressInformation->getAddress());
        } else {
            $quote->setShippingAddress($addressInformation->getAddress());
            $quote->getShippingAddress()->setCollectShippingRates(false)->setShippingMethod(
                $addressInformation->getShippingCarrierCode() . '_' . $addressInformation->getShippingMethodCode()
            );
        }
        $quote->collectTotals();

        return $this->cartTotalRepository->get($cartId);
    }
}
