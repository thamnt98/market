<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: August, 11 2020
 * Time: 3:11 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Plugin\MagentoSalesRule\Model\Rule\Action;

class ToFixed
{
    /**
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\ToFixed $subject
     * @param \Closure                                              $proceed
     * @param float                                                 $qty
     * @param \Magento\SalesRule\Model\Rule                         $rule
     *
     * @return float
     */
    public function aroundFixQuantity(
        \Magento\SalesRule\Model\Rule\Action\Discount\ToFixed $subject,
        \Closure $proceed,
        $qty,
        $rule
    ) {
        $step = $rule->getDiscountStep();
        if ($step) {
            $qty = floor($qty / $step) * $step;
        }

        return $qty;
    }
}
