<?php

namespace SM\Sales\Block\Order\Digital;

use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template;
use SM\Sales\Api\Data\ParentOrderDataInterface;
use SM\Sales\Block\AbstractDetail;
use SM\Sales\Model\DigitalOrderRepository;

/**
 * Class Detail
 * @package SM\Sales\Block\Order\Digital
 */
class Detail extends AbstractDetail
{
    /**
     * @return Template|void
     */
    protected function _prepareLayout()
    {
        $orderId = $this->getRequest()->getParam("id", 0);
        if ($orderId) {
            try {
                $this->setParentOrder($this->digitalOrderRepository->getById($orderId));
                return;
            } catch (\Exception $e) {
                $this->setParentOrder(0);
            }
        }
        $this->setParentOrder(0);
    }
}
