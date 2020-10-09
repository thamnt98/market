<?php

namespace SM\MyVoucher\Model;

use SM\MyVoucher\Api\Data\RuleInterface;


class Rule extends \Magento\Framework\Model\AbstractModel implements RuleInterface
{
    /**
     * Set resource model and Id field name
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_init(ResourceModel\Voucher::class);
        $this->setIdFieldName('id');
    }

    /**
     * @return string|null
     */
    public function getCouponId()
    {
        return $this->_getData(self::COUPON_ID);
    }

    /**
     * @param $couponId
     * @return $this
     */
    public function setCouponId($couponId)
    {
        $this->setData(self::COUPON_ID, $couponId);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_getData(self::CUSTOMER_ID);
    }

    /**
     * @param $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        $this->setData(self::CUSTOMER_ID, $customerId);
        return $this;
    }

}
