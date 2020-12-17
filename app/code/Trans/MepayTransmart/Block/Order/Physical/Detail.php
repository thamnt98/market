<?php
/**
 * @category Trans
 * @package  Trans_MepayTransmart
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\MepayTransmart\Block\Order\Physical;

use SM\Sales\Block\Order\Physical\Detail as ParentDetail;
use Trans\Mepay\Helper\Data as MepayHelper;

class Detail extends ParentDetail
{
    /**
     * @return Template|void
     */
    protected function _prepareLayout()
    {
        $orderId = $this->getRequest()->getParam("id", 0);
        if ($orderId) {
            try {
                $customerId = $this->customerSessionFactory->create()->getCustomerId();
                if (!$customerId) {
                    $order = MepayHelper::getOrderById($orderId); 
                    $customerId = $order->getCustomerId();
                }
                $this->setParentOrder($this->parentOrderRepository->getById($customerId, $orderId));
                return;
            } catch (Exception $e) {
                $this->setParentOrder(0);
            }
        }
        $this->setParentOrder(0);
    }
}