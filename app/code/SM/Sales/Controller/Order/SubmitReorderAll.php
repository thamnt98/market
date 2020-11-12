<?php
/**
 * @category Magento
 * @package SM\Sales\Controller\Order
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Controller\Order;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class ReorderAll
 * @package SM\Sales\Controller\Order
 */
class SubmitReorderAll extends AbstractReorder
{
    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        if (!$this->currentCustomer->getCustomerId()) {
            return $this->_redirect($this->_url->getUrl());
        }

        $data = $this->getRequest()->getPostValue();
        if (isset($data["parent_order_id"])) {
            $parentOrderId = $data["parent_order_id"];
            $result = $this->orderItemRepository
                ->reorderAll(
                    $this->checkoutSession->getQuoteId(),
                    $this->currentCustomer->getCustomerId(),
                    $parentOrderId
                );

            if ($result->getStatus()) {
                $this->messageManager->addSuccessMessage($result->getMessage());
            } else {
                $this->messageManager->addErrorMessage($result->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__("An error occurred"));
        }
        $this->_redirect($this->_redirect->getRefererUrl());
    }
}
