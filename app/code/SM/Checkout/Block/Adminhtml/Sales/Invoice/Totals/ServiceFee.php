<?php

namespace SM\Checkout\Block\Adminhtml\Sales\Invoice\Totals;

use Magento\Framework\DataObject;

class ServiceFee extends \Magento\Backend\Block\Template
{
    /**
     * Get data (totals) source model
     *
     * @return DataObject
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * @return mixed
     */
    public function getInvoice()
    {
        return $this->getParentBlock()->getInvoice();
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
