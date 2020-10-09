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

/**
 * Class Pager
 * @package SM\Sales\Block\Customer\Order
 */
class Pager extends \Magento\Theme\Block\Html\Pager
{
    /**
     * Retrieve page URL by defined parameters
     *
     * @param array $params
     * @return string
     */
    public function getPagerUrl($params = [])
    {
        $urlParams = [];
        $urlParams['_current'] = true;
        $urlParams['_escape'] = true;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_fragment'] = $this->getFragment();
        $urlParams['_query'] = $params;
        return  $this->getUrl($this->getPath(), $urlParams); //producttag defined in routes.xml
    }
}
