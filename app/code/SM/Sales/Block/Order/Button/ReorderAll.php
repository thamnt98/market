<?php
/**
 * @category Magento
 * @package SM\Sales\Block\Order\Button
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Block\Order\Button;

use Magento\Framework\View\Element\Template;

/**
 * Class ReorderAll
 * @package SM\Sales\Block\Order\Button
 */
class ReorderAll extends Template
{
    protected $parentOrderId;

    /**
     * @return int
     */
    public function getParentOrderId()
    {
        return $this->parentOrderId;
    }

    /**
     * @param int $parentOrderId
     * @return ReorderAll
     */
    public function setParentOrderId($parentOrderId)
    {
        $this->parentOrderId = $parentOrderId;
        return $this;
    }

    /**
     * @return string
     */
    public function getReorderAllUrl()
    {
        return $this->getUrl("sales/order/submitreorderall");
    }
}
