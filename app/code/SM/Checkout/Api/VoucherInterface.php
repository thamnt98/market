<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 5/13/20
 * Time: 5:02 PM
 */

namespace SM\Checkout\Api;

/**
 * Interface VoucherInterface
 * @package SM\Checkout\Api
 */
interface VoucherInterface
{
    /**
     * Adds a coupon by code to a specified cart.
     *
     * @param int $cartId The cart ID.
     * @param string $couponCode The coupon code data.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified coupon could not be added.
     */
    public function applyVoucher($cartId, $couponCode);

    /**
     * Deletes a coupon from a specified cart.
     *
     * @param int $cartId The cart ID.
     * @param string $couponCode The coupon code data.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotDeleteException The specified coupon could not be deleted.
     */
    public function remove($cartId, $couponCode);

    /**
     * @param int $cartId The cart ID.
     * @param string $couponCode The coupon code data.
     * @param bool $init
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotDeleteException The specified coupon could not be deleted.
     */
    public function mobileApplyVoucher($cartId, $couponCode, $init = false);
}
