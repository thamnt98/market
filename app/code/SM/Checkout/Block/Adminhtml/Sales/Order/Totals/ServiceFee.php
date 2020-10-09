<?php

namespace SM\Checkout\Block\Adminhtml\Sales\Order\Totals;

use Magento\Framework\DataObject;

class ServiceFee extends \Magento\Backend\Block\Template
{
    /**
     * Retrieve current order model instance
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $this->getParentBlock();
        $this->getOrder();
        $this->getSource();

        if (!$this->getSource()->getServiceFee()) {
            return $this;
        }
        $total = new DataObject(
            [
                'code' => 'service_fee',
                'value' => $this->getSource()->getServiceFee(),
                'label' => __('Service Fee'),
            ]
        );
        $this->getParentBlock()->addTotalBefore($total, 'grand_total');

        return $this;
    }
}
