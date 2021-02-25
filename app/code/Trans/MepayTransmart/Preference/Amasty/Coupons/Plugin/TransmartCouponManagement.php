<?php
/**
 * @category Trans
 * @package  Trans_MepayTransmart
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\MepayTransmart\Preference\Amasty\Coupons\Plugin;

use Amasty\Coupons\Plugin\CouponManagement;
use Amasty\Coupons\Helper\Data;
use Amasty\Coupons\Model\CouponRenderer;
use Magento\Framework\Exception\LocalizedException;

class TransmartCouponManagement extends CouponManagement
{
    /**
     * @var Data
     */
    private $amHelper;

    /**
     * @var CouponRenderer
     */
    private $couponRenderer;

    public function __construct(
        Data $helper,
        CouponRenderer $couponRenderer
    ) {
        $this->amHelper = $helper;
        $this->couponRenderer = $couponRenderer;
        parent::__construct($helper, $couponRenderer);
    }

    /**
     * @param \Magento\Quote\Model\CouponManagement $subject
     * @param string $result
     *
     * @throws LocalizedException
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet($subject, $result)
    {
        // $appliedCoupons = $this->amHelper->getRealAppliedCodes();
        // if (is_array($appliedCoupons)) {
        //     return implode(',', $appliedCoupons);
        // }
        return $result;
    }

    /**
     * @param \Magento\Quote\Model\CouponManagement $subject
     * @param int $cartId The cart ID.
     * @param string $couponCode The coupon code data.
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSet($subject, $cartId, $couponCode)
    {
        $renderedCode = $this->couponRenderer->render($couponCode);
        if (is_string($renderedCode)) {
            return [$cartId, $renderedCode];
        }

        return null;
    }

    /**
     * Temporary fix for checkout compatibility
     *
     * @param \Magento\Quote\Model\CouponManagement $subject
     * @param bool $result
     *
     * @throws LocalizedException
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSet($subject, $result)
    {
        return $this->afterGet($subject, $result);
    }   
}