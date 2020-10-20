<?php

namespace SM\Sales\Plugin\Quote;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\SalesRule\Model\Coupon;
use Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory as CouponCollectionFactory;
use SM\Checkout\Model\VoucherManagement;

class RemoveVoucher
{
    /**
     * Quote repository.
     *
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var CouponCollectionFactory
     */
    protected $couponCollectionFactory;

    /**
     * RemoveVoucher constructor.
     * @param CartRepositoryInterface $quoteRepository
     * @param CouponCollectionFactory $couponCollectionFactory
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        CouponCollectionFactory $couponCollectionFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->couponCollectionFactory = $couponCollectionFactory;
    }

    /**
     * @param VoucherManagement $subject
     * @param int $cartId
     * @param string $couponCode
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeRemove($subject, $cartId, $couponCode)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        $voucherDetailJson = $quote->getData("voucher_detail");
        if (!is_null($voucherDetailJson)) {
            $voucherDetailArray = json_decode($voucherDetailJson, true);
            if (is_array($voucherDetailArray)) {
                $couponCollection = $this->couponCollectionFactory->create()
                    ->addFieldToSelect("code")
                    ->addFieldToFilter("main_table.code", $couponCode)
                    ->join(
                        "salesrule",
                        "salesrule.rule_id = main_table.rule_id",
                        [
                            "name"
                        ]
                    );
                $nameList = [];
                /** @var Coupon $item */
                foreach ($couponCollection as $item) {
                    $nameList[] = $item->getData("name");
                }

                $matches = [];
                foreach ($voucherDetailArray as $key => $detail) {
                    if (!$this->matches($detail["rule_name"], $nameList)) {
                        $matches[] = $detail;
                    }
                }
                $quote->setData("voucher_detail", json_encode($matches));
            }
        }
        return [$cartId, $couponCode];
    }

    /**
     * @param string $name
     * @param array $nameList
     * @return bool
     */
    public function matches($name, $nameList)
    {
        return in_array($name, $nameList);
    }
}
