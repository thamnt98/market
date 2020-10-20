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
 * Class Reorder
 * @package SM\Sales\Controller\Order
 */
class SubmitReorder extends AbstractReorder
{
    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        if (!$this->currentCustomer->getCustomerId()) {
            return $this->_redirect($this->_url->getUrl());
        }

        $data = $this->getRequest()->getPostValue();
        if (isset($data["item_id"])) {
            $itemId = $data["item_id"];
            $result = $this->orderItemRepository
                ->reorder(
                    $this->checkoutSession->getQuoteId(),
                    $itemId
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
