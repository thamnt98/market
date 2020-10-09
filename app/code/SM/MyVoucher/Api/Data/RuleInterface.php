<?php
namespace SM\MyVoucher\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface RuleInterface extends ExtensibleDataInterface
{
    const EXTENSION_CODE = 'myvoucher';
    /**#@+
     * Constants defined for keys of data array
     */
    const COUPON_ID = 'coupon_id';
    const CUSTOMER_ID = 'customer_id';
    /** #@- */

    /**
     * @return string|null
     */
    public function getCouponId();

    /**
     * @param $couponId
     * @return mixed
     */
    public function setCouponId($couponId);

    /**
     * @return string|null
     */
    public function getCustomerId();

    /**
     * @param $customerId
     * @return mixed
     */
    public function setCustomerId($customerId);
}
