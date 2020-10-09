<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 5/7/20
 * Time: 6:21 PM
 */

namespace SM\Checkout\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class BeforeCreateMainOrder
 * @package SM\Checkout\Observer
 */
class BeforeCreateMainOrder implements ObserverInterface
{
    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        $order->setBaseGrandTotal($order->getGrandTotal());

        $shippingAddress = $quote->getShippingAddress();
        $order->setSplitStoreCode($shippingAddress->getSplitStoreCode());
        $order->setStorePickUp($shippingAddress->getStorePickUp());
        $order->setDate($shippingAddress->getDate());
        $order->setTime($shippingAddress->getTime());
        $order->setOarData($shippingAddress->getOarData());

        if ($quote->isMultipleShippingAddresses()) {
            $ship = 0;
            $baseShip = 0;
            foreach ($quote->getAllAddresses() as $address) {
                if ($address->getAddressType() == 'shipping') {
                    foreach ($address->getAllItems() as $item) {
                        if ($item->getFreeShipping()) {
                            $address->setShippingInclTax(0);
                            $address->setBaseShippingInclTax(0);
                            $address->setShippingAmount(0);
                            $address->setBaseShippingAmount(0);
                            break;
                        }
                    }
                    $ship += $address->getShippingInclTax();
                    $baseShip += $address->getBaseShippingInclTax();
                }
            }
            $order->setShippingAmount($ship);
            $order->setBaseShippingAmount($baseShip);
        }

        $subtotal = $baseSubtotal = 0;
        $ship = $baseShip = 0;
        $discount = $baseDiscount = 0;
        foreach ($quote->getAllVisibleItems() as $item) {
            $subtotal += $item->getRowTotal();
            $baseSubtotal += $item->getBaseRowTotal();
            $discount += abs($item->getDiscountAmount());
            $baseDiscount += abs($item->getBaseDiscountAmount());
        }

        /** @var \Magento\Quote\Model\Quote\Address $address */
        foreach ($quote->getAllShippingAddresses() as $address) {
            $ship += $address->getShippingInclTax();
            $baseShip += $address->getBaseShippingInclTax();
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
        // For installment payment
        $serviceFee = 0;
        if ($quote->getData('service_fee')) {
            $serviceFee = (int)$quote->getData('service_fee');
        }
        $order->setSubtotal($subtotal);
        $order->setBaseSubtotal($baseSubtotal);
        $order->setDiscountAmount($discount * -1);
        $order->setBaseDiscountAmount($baseDiscount * -1);
        $order->setGrandTotal($subtotal + $ship - $discount + $serviceFee);
        $order->setBaseGrandTotal($baseSubtotal + $baseShip - $baseDiscount + $serviceFee);
        $order->setTotalAmount('shipping', $ship);
        $order->setBaseTotalAmount('shipping', $baseShip);
    }
}
