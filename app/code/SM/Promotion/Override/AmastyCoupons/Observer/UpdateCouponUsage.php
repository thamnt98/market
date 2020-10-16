<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: October, 12 2020
 * Time: 3:16 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Override\AmastyCoupons\Observer;

class UpdateCouponUsage extends \Amasty\Coupons\Observer\UpdateCouponUsage
{
    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Coupon
     */
    protected $coupon;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Coupon\Usage
     */
    protected $couponUsage;

    /**
     * @var \Amasty\Coupons\Model\CouponRenderer
     */
    protected $couponRenderer;

    /**
     * @var \Magento\SalesRule\Model\CouponFactory
     */
    protected $couponFactory;

    /**
     * Number of coupons used
     *
     * @var array
     */
    protected $timesUsed = [];

    /**
     * Save used coupon code ID
     *
     * @var
     */
    protected $usedCodes = [];

    /**
     * UpdateCouponUsage constructor.
     *
     * @param \Magento\SalesRule\Model\ResourceModel\Coupon       $coupon
     * @param \Magento\SalesRule\Model\ResourceModel\Coupon\Usage $couponUsage
     * @param \Amasty\Coupons\Model\CouponRenderer                $couponRenderer
     * @param \Magento\SalesRule\Model\CouponFactory              $couponFactory
     */
    public function __construct(
        \Magento\SalesRule\Model\ResourceModel\Coupon $coupon,
        \Magento\SalesRule\Model\ResourceModel\Coupon\Usage $couponUsage,
        \Amasty\Coupons\Model\CouponRenderer $couponRenderer,
        \Magento\SalesRule\Model\CouponFactory $couponFactory
    ) {
        $this->coupon = $coupon;
        $this->couponUsage = $couponUsage;
        $this->couponRenderer = $couponRenderer;
        $this->couponFactory = $couponFactory;
    }

    /**
     * @override
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return \Amasty\Coupons\Observer\UpdateCouponUsage
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        if (!$order) {
            return $this;
        }

        // if order placement then increment else if order cancel then decrement
        $increment = (bool)$observer->getEventName() !== 'order_cancel_after';
        $placeBefore = $observer->getEvent()->getName() === 'sales_order_place_before';
        $customerId = $order->getCustomerId();
        $coupons = $this->couponRenderer->parseCoupon($order->getCouponCode());
        if (is_array($coupons) && count($coupons) > 1) {
            foreach ($coupons as $coupon) {
                if ($this->isUsed($coupon, $placeBefore)) {
                    continue;
                }

                /** @var \Magento\SalesRule\Model\Coupon $couponEntity */
                $couponEntity = $this->couponFactory->create();
                $this->coupon->load($couponEntity, $coupon, 'code');

                if ($couponEntity->getId()) {
                    if (!$placeBefore) {
                        $couponEntity->setTimesUsed(
                            $this->getResultTimesUsed($couponEntity) + ($increment ? 1 : -1)
                        );

                        $this->coupon->save($couponEntity);

                        if ($customerId) {
                            $this->couponUsage->updateCustomerCouponTimesUsed(
                                $customerId,
                                $couponEntity->getId(),
                                $increment
                            );
                        }
                    } else {
                        $this->timesUsed['coupon_times_used'][$couponEntity->getId()] = $couponEntity->getTimesUsed();
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @param string $code
     * @param bool $placeBefore
     *
     * @return bool
     */
    protected function isUsed($code, $placeBefore)
    {
        if (!isset($this->usedCodes[$code])) {
            if (!$placeBefore) {
                $this->usedCodes[$code] = 1;
            }
            return false;
        }

        return true;
    }

    /**
     * Magento add value in column 'times_used' in DB. We also add value in column 'times_used'.
     * In this method we override this value on general solution
     *
     * @param \Magento\SalesRule\Model\Coupon $couponEntity
     *
     * @return int
     */
    protected function getResultTimesUsed($couponEntity)
    {
        if (!isset($this->timesUsed['coupon_times_used'][$couponEntity->getId()])) {
            return $couponEntity->getTimesUsed();
        } else {
            return (int) $this->timesUsed['coupon_times_used'][$couponEntity->getId()];
        }
    }
}
