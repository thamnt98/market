<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 5/9/20
 * Time: 10:58 AM
 */

namespace SM\Checkout\Plugin\Multishipping\Model\Cart;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Cart\CartTotalRepository;
use Magento\Quote\Model\Cart\Totals;

class CartTotalRepositoryPlugin
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param CartTotalRepository $subject
     * @param Totals $quoteTotals
     * @param String $cartId
     * @return Totals
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGet(
        CartTotalRepository $subject,
        Totals $quoteTotals,
        String $cartId
    ) {

        $quote = $this->quoteRepository->getActive($cartId);
        if ($quote->getIsMultiShipping()) {
            $ship = 0;
            $baseShip = 0;
            foreach ($quote->getAllAddresses() as $address) {
                if ($address->getAddressType() == 'shipping') {
                    foreach ($address->getAllItems() as $item) {
                        if ($item->getFreeShipping()) {
                            $address->setShippingInclTax(0);
                            $address->setBaseShippingInclTax(0);
                            break;
                        }
                    }
                    $ship += $address->getShippingInclTax();
                    $baseShip += $address->getBaseShippingInclTax();
                }
            }
            $quoteTotals->setBaseShippingAmount((float)$baseShip);
            $quoteTotals->setShippingAmount((float)$ship);

            //Calculate Service fee for installment
            $serviceFee = 0;
            if ($quote->getData('service_fee')) {
                $serviceFee = (int)$quote->getData('service_fee');
            }

            $quoteTotals->setGrandTotal(
                $quoteTotals->getSubtotal() + $ship - abs($quoteTotals->getDiscountAmount()) + $serviceFee
            )->setBaseGrandTotal(
                $quoteTotals->getBaseSubtotal() + $baseShip - abs($quoteTotals->getBaseDiscountAmount())+$serviceFee
            );
        }
        return $quoteTotals;
    }
}
