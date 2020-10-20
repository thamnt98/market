<?php

namespace SM\Review\Block\Product;

use Magento\Framework\View\Element\Template;

/**
 * Class Button
 * @package SM\Review\Block\Product
 */
class Button extends Template
{
    private $orderId;

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param int $invoiceId
     * @return Button
     */
    public function setOrderId($invoiceId)
    {
        $this->orderId = $invoiceId;
        return $this;
    }
}
