<?php
namespace SM\Checkout\Plugin\Quote\Model\Cart;

use Magento\Quote\Api\Data\TotalsExtensionFactory;
use Magento\Quote\Api\Data\TotalsInterface;
use Magento\Quote\Model\Cart\CartTotalRepository as TotalRepository;
use Magento\Quote\Model\Quote;

class CartTotalRepository
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var TotalsExtensionFactory
     */
    protected $totalsExtensionFactory;

    /**
     * @var \SM\Checkout\Api\Data\CheckoutWeb\VoucherInterfaceFactory
     */
    protected $voucherInterfaceFactory;

    /**
     * CartTotalRepository constructor.
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param TotalsExtensionFactory $totalsExtensionFactory
     * @param \SM\Checkout\Api\Data\CheckoutWeb\VoucherInterfaceFactory $voucherInterfaceFactory
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        TotalsExtensionFactory $totalsExtensionFactory,
        \SM\Checkout\Api\Data\CheckoutWeb\VoucherInterfaceFactory $voucherInterfaceFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->totalsExtensionFactory = $totalsExtensionFactory;
        $this->voucherInterfaceFactory = $voucherInterfaceFactory;
    }

    /**
     * @param TotalRepository $subject
     * @param TotalsInterface $totals
     * @param int $cartId
     * @return TotalsInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(
        TotalRepository $subject,
        TotalsInterface $totals,
        $cartId
    ) {
        /** @var Quote  $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        /** @var \Magento\Quote\Api\Data\TotalsExtensionInterface $extensionAttributes */
        $extensionAttributes = $totals->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->totalsExtensionFactory->create();
        }
        $couponList = $quote->getCouponCode();
        if ($couponList && $couponList != '') {
            $couponList = explode(",", $couponList);
        } else {
            $couponList = [];
        }
        $applyVoucherList = $quote->getApplyVoucher();
        if ($applyVoucherList && $applyVoucherList != '') {
            $applyVoucherList = explode(",", $applyVoucherList);
        } else {
            $applyVoucherList = [];
        }
        $voucherList = $this->getNotApplyVoucherList(array_diff($applyVoucherList, $couponList));
        $extensionAttributes->setApplyVoucher($voucherList);
        /** @var \Magento\Quote\Model\Quote\Address $address */
        foreach ($quote->getAllShippingAddresses() as $address) {
            if ($address->getShippingMethod() && $address->getFreeShipping()) {
                $extensionAttributes->setFreeShippingDiscount(
                    $address->getShippingDiscountAmount() + $extensionAttributes->getFreeShippingDiscount()
                );
            }
        }

        $this->changeTotal($quote,$totals);
        $totals->setExtensionAttributes($extensionAttributes);
        return $totals;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param $total
     */
    public function changeTotal($quote, $total){
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
        $total->setSubtotal($subtotal);
        $total->setBaseSubtotal($baseSubtotal);
        $total->setDiscountAmount($discount * -1);
        $total->setBaseDiscountAmount($baseDiscount * -1);
        $total->setGrandTotal($subtotal + $ship - $discount + $serviceFee);
        $total->setBaseGrandTotal($baseSubtotal + $baseShip - $baseDiscount + $serviceFee);
        $total->setTotalAmount('shipping', $ship);
        $total->setBaseTotalAmount('shipping', $baseShip);
    }
    /**
     * @param $voucherList
     * @return array
     */
    protected function getNotApplyVoucherList($voucherList)
    {
        $notApplyVoucherList = [];
        foreach ($voucherList as $code) {
            $notApplyVoucherList[] = $this->voucherInterfaceFactory->create()->setCode($code)->setAmount(0);
        }
        return $notApplyVoucherList;
    }
}
