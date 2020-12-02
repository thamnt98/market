<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 5/13/20
 * Time: 5:11 PM
 */

namespace SM\Checkout\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CouponManagementInterface;

/**
 * Class VoucherManagement
 * @package SM\Checkout\Model
 */
class VoucherManagement implements \SM\Checkout\Api\VoucherInterface
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var CouponManagementInterface
     */
    protected $couponManagement;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * VoucherManagement constructor.
     *
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param CouponManagementInterface                   $couponManagement
     * @param \Magento\Quote\Api\CartRepositoryInterface  $quoteRepository
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        CouponManagementInterface $couponManagement,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->couponManagement = $couponManagement;
        $this->quoteRepository = $quoteRepository;
        $this->messageManager = $messageManager;
    }

    /**
     * @param $cartId
     * @param $couponCode
     *
     * @return bool
     * @throws NoSuchEntityException
     * @throws \Exception
     */
    public function applyVoucher($cartId, $couponCode)
    {
        if (empty($couponCode) || empty($cartId)) {
            throw new \Exception(
                __("The coupon code couldn't be applied. Verify the coupon code and try again.")
            );
        }

        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        $oldVoucher = [$couponCode];
        $coupons = [$couponCode];

        if (!empty($quote->getCouponCode())) {
            $oldCoupons = explode(',', $quote->getCouponCode());
            if (in_array($couponCode, explode(',', $quote->getCouponCode()))) {
                // This coupon has been applied.
                return true;
            } else {
                $coupons = array_merge($coupons, $oldCoupons);
            }
        }

        if (!empty($quote->getData('apply_voucher'))) {
            $oldVoucher = array_unique(
                array_merge(
                    $oldVoucher,
                    explode(',', $quote->getData('apply_voucher'))
                )
            );
        }

        if (strpos($_SERVER['HTTP_REFERER'], 'myvoucher/voucher') !== false) {
            // Add notify when apply from my voucher page.
            $this->messageManager->addSuccessMessage(__('Your voucher has been applied.'));
        }

        $voucherAppliedStr = implode(',', $oldVoucher);
        $couponsStr = implode(',', $coupons);
        if (!$quote->getItemsCount()) { // Set voucher failed
            $quote->setData('apply_voucher', $voucherAppliedStr)
                ->setData('totals_collected_flag', true); // not collect total
            $this->quoteRepository->save($quote);
            throw new \Exception(__('The "%1" Cart doesn\'t contain products.', $cartId));
        }

        if (!$quote->getStoreId()) {
            throw new \Exception(__('Cart isn\'t assigned to correct store'));
        }

        $quote->getShippingAddress()->setCollectShippingRates(true);

        try {
            $quote->setCouponCode($couponsStr)
                ->setData('apply_voucher', $voucherAppliedStr)
                ->setData('totals_collected_flag', false);
            $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
            throw new \Exception(
                __("The coupon code couldn't be applied. Verify the coupon code and try again."),
                $e
            );
        }

        if (!in_array($couponCode, explode(',', $quote->getCouponCode()))) {
            throw new \Exception(__("The coupon code isn't valid. Verify the code and try again."));
        }

        return true;
    }
    /**
     * Deletes a coupon from a specified cart.
     *
     * @param int $cartId The cart ID.
     * @param string $couponCode The coupon code data.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotDeleteException The specified coupon could not be deleted.
     */
    public function remove($cartId, $couponCode)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new \Exception(__('The "%1" Cart doesn\'t contain products.', $cartId));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);
        try {
            if ($quote->getApplyVoucher() && $quote->getApplyVoucher() != '') {
                $oldApplyCoupon = explode(',', $quote->getApplyVoucher());
            } else {
                $oldApplyCoupon = [];
            }
            if (in_array($couponCode, $oldApplyCoupon)) {
                $newApplyCoupon = array_diff($oldApplyCoupon, [$couponCode]);
                $newApplyCoupon = implode(',', $newApplyCoupon);
                $quote->setApplyVoucher($newApplyCoupon);
            }

            $oldCoupon = explode(',', $quote->getCouponCode());
            $newCoupon = array_diff($oldCoupon, [$couponCode]);
            $newCoupon = implode(',', $newCoupon);
            $quote->setCouponCode($newCoupon);
            $this->quoteRepository->save($quote->collectTotals());
        } catch (\Exception $e) {
            throw new \Exception(
                __("The coupon code couldn't be deleted. Verify the coupon code and try again." . $e->getMessage())
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function mobileApplyVoucher($cartId, $couponCode, $init = false)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        if (!$quote->getItemsCount()) {
            throw new \Exception(__('The "%1" Cart doesn\'t contain products.', $cartId));
        }
        if (!$quote->getStoreId()) {
            throw new \Exception(__('Cart isn\'t assigned to correct store'));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);

        try {
            if ($init) {
                $couponCode = $quote->getApplyVoucher();
            }
            $newApplyCoupon = explode(',', $couponCode);

            if ($quote->getApplyVoucher() && $quote->getApplyVoucher() != '') {
                $oldApplyCoupon = explode(',', $quote->getApplyVoucher());
            } else {
                $oldApplyCoupon = [];
            }

            if (!empty(array_diff($oldApplyCoupon, $newApplyCoupon)) || !empty(array_diff($newApplyCoupon, $oldApplyCoupon))) {
                $quote->setApplyVoucher($couponCode);
            }

            if ($quote->getCouponCode() && $quote->getCouponCode() != '') {
                $oldCoupon = explode(',', $quote->getCouponCode());
            } else {
                $oldCoupon = [];
            }

            if (!empty(array_diff($oldCoupon, $newApplyCoupon)) || !empty(array_diff($newApplyCoupon, $oldCoupon))) {
                $quote->setCouponCode($couponCode);
            }

            $quote->collectTotals();
            $this->quoteRepository->save($quote);

        } catch (LocalizedException $e) {
            throw new \Exception(__("The coupon code couldn't be applied: " . $e->getMessage()));
        } catch (\Exception $e) {
            throw new \Exception(__("The coupon code couldn't be applied. Verify the coupon code and try again."));
        }
        /*$couponAfterCollect = explode(',', $quote->getCouponCode());
        if (!in_array($couponCode, $couponAfterCollect)) {
            throw new NoSuchEntityException(__("The coupon code isn't valid. Verify the code and try again."));
        }*/
        return true;
    }
}
