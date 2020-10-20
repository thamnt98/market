<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Checkout
 *
 * Date: July, 30 2020
 * Time: 3:56 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Checkout\Plugin\MagentoTax\Model\Sales\Total\Quote;

class Shipping
{
    public function afterCollect(
        \Magento\Tax\Model\Sales\Total\Quote\Shipping $subject,
        $result,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        /** @var \Magento\Quote\Model\Quote\Address $address */
        $address = $shippingAssignment->getShipping()->getAddress();
        $address->setShippingAmount($total->getTotalAmount('shipping'));
        $address->setBaseShippingAmount($total->getBaseTotalAmount('shipping'));

        return $result;
    }
}
