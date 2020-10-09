<?php
namespace SM\MyVoucher\Api;


interface RuleRepositoryInterface
{
    /**
     * @api
     * @param int $customerId
     * @param string $query
     * @return \SM\MyVoucher\Api\Data\RuleDataInterface[]
     */
    public function getVoucherByCustomer($customerId,$query = '');

    /**
     * @param int $customerId
     * @param int $voucherId
     * @return \SM\MyVoucher\Api\Data\RuleDataInterface[]
     */
    public function getVoucherDetailByCustomer($customerId, $voucherId);

    /**
     * @api
     * @param int $cartId
     * @param string $couponCode
     * @return boolean
     */
    public function applyVoucher($cartId, $couponCode);

    /**
     * @param int $customerId
     * @return int
     */
    public function getCountVoucher($customerId);
}
