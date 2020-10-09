<?php

namespace SM\Promotion\Plugin;

use Amasty\Coupons\Helper\Data;
use Amasty\Coupons\Model\CouponRenderer;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address;
use Magento\SalesRule\Model\Coupon;
use Magento\SalesRule\Model\ResourceModel\Coupon\UsageFactory;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Utility;

class DayOfWeekRule
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * DayOfWeekRule constructor.
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->date = $date;
    }

    /**
     * Check if rule can be applied for specific address/quote/customer
     * @param Utility $subject
     * @param \Closure $proceed
     * @param Rule $rule
     * @param Address $address -
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws LocalizedException
     * phpcs:ignore
     */
    public function aroundCanProcessRule(Utility $subject, \Closure $proceed, $rule, $address)
    {
        /** @var null|string $dayOfWeek */

        $dayOfWeek = $rule->getData('day_of_week');
        if (!empty($dayOfWeek)) {
            $days = explode(",", $dayOfWeek);

            $today = $this->date->date('N');
            if (!in_array($today, $days)) {
                return false;
            }
        }

        return $proceed($rule, $address);
    }
}
