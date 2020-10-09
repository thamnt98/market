<?php
/**
 * SM\TobaccoAlcoholProduct\Plugin\GTM\Helper
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\TobaccoAlcoholProduct\Plugin\GTM\Helper;

/**
 * Class Data
 * @package SM\TobaccoAlcoholProduct\Plugin\GTM\Helper
 */
class Data
{
    /**
     * @param \SM\GTM\Helper\Data $subject
     * @param \Magento\Customer\Api\Data\CustomerInterface $result
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function afterGetGtmCustomerInfo($subject, $result) {
        if (!$result->getCustomAttribute("is_alcohol_informed")) {
            $result->setCustomAttribute("is_alcohol_informed", "0");
        }
        return $result;
    }
}
