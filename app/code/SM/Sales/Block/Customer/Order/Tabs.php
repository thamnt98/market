<?php
/**
 * @category Magento
 * @package SM\Sales\Block\Customer\Order
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Block\Customer\Order;

use Magento\Framework\View\Element\Template;

/**
 * Class Tabs
 * @package SM\Sales\Block\Customer\Order
 */
class Tabs extends Template
{
    /**
     * @param string $type
     * @return bool
     */
    public function checkTabActive($type)
    {
        $tab = $this->_request->getParam('tab', 'in-progress');
        if ($tab == $type) {
            return true;
        } else {
            return false;
        }
    }

}
