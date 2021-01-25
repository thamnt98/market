<?php
/**
 * Class Search
 * @package SM\Checkout\Plugin\Quote
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Checkout\Plugin\Quote\Model\Quote\Address\Total;

class Shipping extends \Magento\Quote\Model\Quote\Address\Total\Shipping
{
    const STORE_PICK_UP  = 'store_pickup_store_pickup';
    const NOT_SHIP  = 'transshipping_transshipping';
    const NOT_AVAILABLE  = 'transshipping_transshipping0';
    const DEFAULT_METHOD = 'transshipping_transshipping1';
    const SAME_DAY       = 'transshipping_transshipping2';
    const SCHEDULE       = 'transshipping_transshipping3';
    const NEXT_DAY       = 'transshipping_transshipping4';
    const DC             = 'transshipping_transshipping5';
    const TRANS_COURIER  = 'transshipping_transshipping6';

    /**
     * @param \Magento\Quote\Model\Quote\Address\Total\Shipping $subject
     * @param $result
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return mixed
     */
    public function afterCollect(
        \Magento\Quote\Model\Quote\Address\Total\Shipping $subject,
        $result,
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        $address = $shippingAssignment->getShipping()->getAddress();
        $method = $shippingAssignment->getShipping()->getMethod();
        if ($total->getShippingDescription()) {
            return $result;
        }
        if ($method) {
            $shippingList = [];
            foreach ($address->getAllShippingRates() as $rate) {
                if (strpos($rate->getCode(), 'transshipping') !== false) {
                    $shippingList[$rate->getCode()] = $rate;
                }
            }
            if (!empty($shippingList)) {
                ksort($shippingList);
                if (isset($shippingList[self::DC])) {
                    $rate = $shippingList[self::DC];
                } elseif (isset($shippingList[self::TRANS_COURIER])) {
                    $rate = $shippingList[self::TRANS_COURIER];
                } else {
                    $rate = reset($shippingList);
                }
                $address->setShippingMethod($rate->getCode());
                $store = $quote->getStore();
                $amountPrice = $this->priceCurrency->convert(
                    $rate->getPrice(),
                    $store
                );
                $total->setTotalAmount($this->getCode(), $amountPrice);
                $total->setBaseTotalAmount($this->getCode(), $rate->getPrice());
                $shippingDescription = $rate->getCarrierTitle() . ' - ' . $rate->getMethodTitle();
                $address->setShippingDescription(trim($shippingDescription, ' -'));
                $total->setBaseShippingAmount($rate->getPrice());
                $total->setShippingAmount($amountPrice);
                $total->setShippingDescription($address->getShippingDescription());
            }
        }
        return $result;
    }
}
