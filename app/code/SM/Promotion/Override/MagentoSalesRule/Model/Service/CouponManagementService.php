<?php


namespace SM\Promotion\Override\MagentoSalesRule\Model\Service;


class CouponManagementService extends \Magento\SalesRule\Model\Service\CouponManagementService
{
    protected function convertCouponSpec(\Magento\SalesRule\Api\Data\CouponGenerationSpecInterface $couponSpec)
    {
        $data = parent::convertCouponSpec($couponSpec);
        $extAttribute = $couponSpec->getExtensionAttributes();
        $data['customer_id'] = $extAttribute->getCustomerId();
        return $data;
    }
}