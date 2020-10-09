<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Checkout
 *
 * Date: August, 20 2020
 * Time: 11:44 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Checkout\Plugin\TransSprint\Model\Total;

class ServiceFee
{
    /**
     * @param \Trans\Sprint\Model\Total\ServiceFee                $subject
     * @param \Magento\Quote\Model\Quote                          $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total            $total
     *
     * @return array
     */
    public function beforeCollect(
        \Trans\Sprint\Model\Total\ServiceFee $subject,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        if (!is_null($quote->getData('service_fee_request'))) {
            $quote->setData(
                'service_fee',
                round(
                    (float)$total->getData('grand_total') *
                    (float)$quote->getData('service_fee_request') /
                    100
                )
            );

            $quote->setData('service_fee_request', null);
        }

        return [$quote, $shippingAssignment, $total];
    }
}
