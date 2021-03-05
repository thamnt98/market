<?php
namespace SM\Checkout\Preference\Quote\Model\Quote;

use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\Total\CollectorInterface;

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

        $this->checkoutSession->setIsMultipleShippingAddresses(true);
        $shippingDefaultId = $quote->isVirtual() ? 0 : $quote->getShippingAddress()->getId();
        $addresses = $quote->getAllAddresses();
        $newAddress = [];
        // Collect quote shipping address finally
        if ($shippingDefaultId !== 0) {
            foreach ($addresses as $index => $address) {
                if ($address->getId() == $shippingDefaultId ||
                    (is_null($shippingDefaultId) && $address->getAddressType() === 'shipping')
                ) {
                    $addresses['main'] = $address;
                    break;
                }
            }
        }
        //$newAddress = $newAddress + $addresses;
        foreach ($addresses as $key => $address) {
            if ($address->getAddressType() === 'shipping') {
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

    /**
     * Collect address total.
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address $address
     * @return Address\Total
     */
    public function collectAddressTotals(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Model\Quote\Address $address
    ) {
        /** @var \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment */
        $shippingAssignment = $this->shippingAssignmentFactory->create();

        /** @var \Magento\Quote\Api\Data\ShippingInterface $shipping */
        $shipping = $this->shippingFactory->create();
        $shipping->setMethod($address->getShippingMethod());
        $shipping->setAddress($address);
        $shippingAssignment->setShipping($shipping);
        $shippingAssignment->setItems($address->getAllItems());

        /** @var \Magento\Quote\Model\Quote\Address\Total $total */
        $total = $this->totalFactory->create(\Magento\Quote\Model\Quote\Address\Total::class);
        $this->eventManager->dispatch(
            'sales_quote_address_collect_totals_before',
            [
                'quote' => $quote,
                'shipping_assignment' => $shippingAssignment,
                'total' => $total
            ]
        );

        foreach ($this->collectorList->getCollectors($quote->getStoreId()) as $collector) {
            $message = get_class($collector) . '. Thoi gian xu ly collect cho quoteID ' . $quote->getId() . ': ';
            $dateStart = microtime(true); // log_time
            /** @var CollectorInterface $collector */
            $collector->collect($quote, $shippingAssignment, $total);
            $dateEnd = microtime(true); // log_time
            $this->writeTimeLog($dateEnd, $dateStart, $message);
        }

        $this->eventManager->dispatch(
            'sales_quote_address_collect_totals_after',
            [
                'quote' => $quote,
                'shipping_assignment' => $shippingAssignment,
                'total' => $total
            ]
        );

        $address->addData($total->getData());
        $address->setAppliedTaxes($total->getAppliedTaxes());
        return $total;
    }

    /**
     * @param $dateEnd
     * @param $dateStart
     * @param $message
     */
    protected function writeTimeLog($dateEnd, $dateStart, $message)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/checkout-log-time.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $timeDiff = round($dateEnd - $dateStart, 4);
        $logger->info($message . $timeDiff . 's');
    }
}
