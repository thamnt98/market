<?php
namespace SM\Checkout\Preference\Quote\Model\Quote;

/**
 * Class TotalsCollector
 * @package SM\Checkout\Preference\Quote\Model\Quote
 */
class TotalsCollector extends \Magento\Quote\Model\Quote\TotalsCollector
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * TotalsCollector constructor.
     *
     * @param \Magento\Checkout\Model\Session                           $checkoutSession
     * @param \Magento\Quote\Model\Quote\Address\Total\Collector        $totalCollector
     * @param \Magento\Quote\Model\Quote\Address\Total\CollectorFactory $totalCollectorFactory
     * @param \Magento\Framework\Event\ManagerInterface                 $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager
     * @param \Magento\Quote\Model\Quote\Address\TotalFactory           $totalFactory
     * @param \Magento\Quote\Model\Quote\TotalsCollectorList            $collectorList
     * @param \Magento\Quote\Model\ShippingFactory                      $shippingFactory
     * @param \Magento\Quote\Model\ShippingAssignmentFactory            $shippingAssignmentFactory
     * @param \Magento\Quote\Model\QuoteValidator                       $quoteValidator
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\Quote\Address\Total\Collector $totalCollector,
        \Magento\Quote\Model\Quote\Address\Total\CollectorFactory $totalCollectorFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Quote\Model\Quote\Address\TotalFactory $totalFactory,
        \Magento\Quote\Model\Quote\TotalsCollectorList $collectorList,
        \Magento\Quote\Model\ShippingFactory $shippingFactory,
        \Magento\Quote\Model\ShippingAssignmentFactory $shippingAssignmentFactory,
        \Magento\Quote\Model\QuoteValidator $quoteValidator
    ) {
        parent::__construct(
            $totalCollector,
            $totalCollectorFactory,
            $eventManager,
            $storeManager,
            $totalFactory,
            $collectorList,
            $shippingFactory,
            $shippingAssignmentFactory,
            $quoteValidator
        );

        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Collect quote.
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return \Magento\Quote\Model\Quote\Address\Total
     */
    public function collect(\Magento\Quote\Model\Quote $quote)
    {
        /** @var \Magento\Quote\Model\Quote\Address\Total $total */
        $total = $this->totalFactory->create(\Magento\Quote\Model\Quote\Address\Total::class);
        $this->eventManager->dispatch('sales_quote_collect_totals_before', ['quote' => $quote]);
        $this->_collectItemsQtys($quote);

        $total->setSubtotal(0);
        $total->setBaseSubtotal(0);
        $total->setSubtotalWithDiscount(0);
        $total->setBaseSubtotalWithDiscount(0);
        $total->setGrandTotal(0);
        $total->setBaseGrandTotal(0);

        $this->checkoutSession->setIsMultipleShippingAddresses($quote->isMultipleShippingAddresses());
        $shippingDefaultId = $quote->isVirtual() ? 0 : $quote->getShippingAddress()->getId();
        $addresses = $quote->getAllAddresses();

        // Collect quote shipping address finally
        if ($shippingDefaultId !== 0 && $quote->isMultipleShippingAddresses()) {
            foreach ($addresses as $index => $address) {
                if ($address->getId() == $shippingDefaultId ||
                    (is_null($shippingDefaultId) && $address->getAddressType() === 'shipping')
                ) {
                    $addresses['main'] = $address;
                    break;
                }
            }
        }

        foreach ($addresses as $key => $address) {
            if ($quote->isMultipleShippingAddresses() && $address->getAddressType() === 'shipping') {
                if ($key === 'main') { // Main Address (Quote shipping address)
                    $this->checkoutSession->setMainOrder(true);
                    $addressTotal = $this->collectAddressTotals($quote, $address);

                    if ($this->checkoutSession->getMainAddress()) {
                        $this->checkoutSession->unsMainAddress();
                    }

                    $this->checkoutSession->unsMainOrder();
                } else { // Other shipping address
                    $this->collectAddressTotals($quote, $address);
                    if ($address->getId()
                        && isset($addresses['main'])
                        && $address->getId() == $addresses['main']->getId()
                    ) {
                        $this->checkoutSession->setMainAddress(clone $address);
                    }

                    continue;
                }
            } else {
                $addressTotal = $this->collectAddressTotals($quote, $address);
            }

            $total->setShippingAmount($addressTotal->getShippingAmount());
            $total->setBaseShippingAmount($addressTotal->getBaseShippingAmount());
            $total->setShippingDescription($addressTotal->getShippingDescription());

            $total->setSubtotal((float)$total->getSubtotal() + $addressTotal->getSubtotal());
            $total->setBaseSubtotal((float)$total->getBaseSubtotal() + $addressTotal->getBaseSubtotal());

            $total->setSubtotalWithDiscount(
                (float)$total->getSubtotalWithDiscount() + $addressTotal->getSubtotalWithDiscount()
            );
            $total->setBaseSubtotalWithDiscount(
                (float)$total->getBaseSubtotalWithDiscount() + $addressTotal->getBaseSubtotalWithDiscount()
            );

            $total->setGrandTotal((float)$total->getGrandTotal() + $addressTotal->getGrandTotal());
            $total->setBaseGrandTotal((float)$total->getBaseGrandTotal() + $addressTotal->getBaseGrandTotal());
        }
        if ($quote->getData('service_fee')) {
            $amount = (int)$quote->getData('service_fee');
            $total->addTotalAmount('service_fee', $amount);
            $total->addBaseTotalAmount('service_fee', $amount);
            $total->setServiceFee($amount);

            $total->setGrandTotal($total->getGrandTotal() + $amount);
            $total->setBaseGrandTotal($total->getBaseGrandTotal() + $amount);
        }

        $this->checkoutSession->unsIsMultipleShippingAddresses();
        $this->quoteValidator->validateQuoteAmount($quote, $quote->getGrandTotal());
        $this->quoteValidator->validateQuoteAmount($quote, $quote->getBaseGrandTotal());
        $this->_validateCouponCode($quote);
        $this->eventManager->dispatch(
            'sales_quote_collect_totals_after',
            ['quote' => $quote]
        );
        return $total;
    }
}
