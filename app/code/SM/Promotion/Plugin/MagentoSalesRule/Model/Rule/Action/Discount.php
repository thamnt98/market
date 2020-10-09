<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: August, 11 2020
 * Time: 3:03 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Plugin\MagentoSalesRule\Model\Rule\Action;

class Discount
{
    /**
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\DiscountInterface $subject
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\Data              $result
     * @param \Magento\SalesRule\Model\Rule                                   $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem                    $item
     *
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     */
    public function afterCalculate(
        \Magento\SalesRule\Model\Rule\Action\Discount\DiscountInterface $subject,
        $result,
        $rule,
        $item
    ) {
        if ($result->getAmount() < 0) {
            $result->setAmount(0)
                ->setBaseAmount(0)
                ->setOriginalAmount(0)
                ->setBaseOriginalAmount(0);
        }

        if (!$item->getId() && property_exists($subject, 'cachedDiscount')) {
            $subject::$cachedDiscount = [];
        }

        return $result;
    }
}
