<?php

declare(strict_types=1);

namespace SM\Checkout\Plugin;

class Address
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Address constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $subject
     * @param $result
     * @return mixed
     */
    public function afterGetAllItems(
        \SM\Checkout\Preference\Quote\Model\Quote\Address $subject,
        $result
    ) {
        if ($this->checkoutSession->getMainOrder() && $this->checkoutSession->getIsMultipleShippingAddresses()) {
            $items = [];
            foreach ($subject->getQuote()->getItemsCollection() as $aItem) {
                if ($aItem->isDeleted() || !$subject->quoteItemActive($aItem)) {
                    continue;
                }

                $items[] = $aItem;
            }

            return $items;
        }

        return $result;
    }
}
