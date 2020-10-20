<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 3/24/20
 * Time: 1:53 PM
 */

namespace SM\Checkout\Model\Total;

use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;

class SubtractInactiveItem extends AbstractTotal
{
    /**
     * @var \SM\Checkout\Helper\DigitalProduct
     */
    private $digitalHelper;

    /**
     * SubstractInactiveItem constructor.
     * @param \SM\Checkout\Helper\DigitalProduct $digitalHelper
     */
    public function __construct(
        \SM\Checkout\Helper\DigitalProduct $digitalHelper
    ) {
        $this->digitalHelper = $digitalHelper;
    }

    /**
     * substract inactive items and re-calculate main orders totals
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {

        parent::collect($quote, $shippingAssignment, $total);

        $items = $shippingAssignment->getItems();
        if (!count($items) ||
            $shippingAssignment->getShipping()->getAddress()->getId() != $quote->getShippingAddress()->getId()
        ) { // Only main address
            return $this;
        }

        $this->updateShippingFee($quote, $total);

        return $this;
    }

    /**
     * @param Quote $quote
     * @param Total $total
     *
     * @return self
     */
    protected function updateShippingFee($quote, $total)
    {
        if ($quote->isMultipleShippingAddresses()) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $subtotal = $baseSubtotal = 0;
            $ship = $baseShip = 0;
            $discount = $baseDiscount = 0;
            foreach ($quote->getAllVisibleItems() as $item) {
                $subtotal += $item->getRowTotal();
                $baseSubtotal += $item->getBaseRowTotal();

                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildren() as $child) {
                        $discount += abs($child->getDiscountAmount());
                        $baseDiscount += abs($child->getBaseDiscountAmount());
                    }
                } else {
                    $discount += abs($item->getDiscountAmount());
                    $baseDiscount += abs($item->getBaseDiscountAmount());
                }
            }

            /** @var \Magento\Quote\Model\Quote\Address $address */
            foreach ($quote->getAllShippingAddresses() as $address) {
                $ship += $address->getShippingAmount();
                $baseShip += $address->getBaseShippingAmount();
                if (!$address->getFreeShipping()) {
                    $discount += abs($address->getShippingDiscountAmount());
                    $baseDiscount += abs($address->getBaseShippingDiscountAmount());
                }
            }

            /** @var \Magento\Quote\Model\Quote\Address $address */
            foreach ($quote->getAllShippingAddresses() as $address) {
                $address->setDiscountAmount($discount * -1);
                $address->setBaseDiscountAmount($baseDiscount * -1);
            }

            $total->setSubtotal($subtotal);
            $total->setBaseSubtotal($baseSubtotal);
            $total->setDiscountAmount($discount * -1);
            $total->setBaseDiscountAmount($baseDiscount * -1);
            $total->setGrandTotal($subtotal + $ship - $discount);
            $total->setBaseGrandTotal($baseSubtotal + $baseShip - $baseDiscount);
            $total->setTotalAmount('shipping', $ship);
            $total->setBaseTotalAmount('shipping', $baseShip);
            $logger->info($ship);
        }

        return $this;
    }
}
