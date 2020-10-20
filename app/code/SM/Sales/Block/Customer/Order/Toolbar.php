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
 * Class Toolbar
 * @package SM\Sales\Block\Customer\Order
 */
class Toolbar extends Template
{
    /**
     * @var string
     */
    protected $tab;

    /**
     * @return string
     */
    public function getTab()
    {
        return $this->tab;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setTab($value)
    {
        $this->tab = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->getUrl('*/*/*', ['tab' => $this->tab, '_current' => true, '_use_rewrite' => true]);
    }

    /**
     * @return string
     */
    public function getUrlWithoutParams()
    {
        return $this->getUrl('*/*/*', ['tab' => $this->tab, '_use_rewrite' => true]);
    }
}
