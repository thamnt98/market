<?php
/**
 * @category    SM
 * @package     SM_Customer
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Nam Nguyen <namnd2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace Trans\Customer\Plugin\Customer\Block\Account;

/**
 * Class AccountManagement
 */
class Navigation
{
    /**
     * @param \Magento\Customer\Block\Account\Navigation $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\View\Element\AbstractBlock $link
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundRenderLink(
        \Magento\Customer\Block\Account\Navigation $subject,
        \Closure $proceed,
        \Magento\Framework\View\Element\AbstractBlock $link
    ) {
        $result = $proceed($link);
        if ($link->hasCssClass()) {
            $result = str_replace("nav item", $link->getCssClass() . " nav item", $result);
        }
        return $result;
    }
}
