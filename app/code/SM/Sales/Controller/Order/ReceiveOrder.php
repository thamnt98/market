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

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\InputException;
use SM\Sales\Model\SubOrderRepository;

/**
 * Class ReceiveOrder
 * @package SM\Sales\Controller\Order
 */
class ReceiveOrder extends Action
{
    /**
     * @var SubOrderRepository
     */
    protected $subOrderRepository;

    /**
     * ReceiveOrder constructor.
     * @param Context $context
     * @param SubOrderRepository $subOrderRepository
     */
    public function __construct(
        Context $context,
        SubOrderRepository $subOrderRepository
    ) {
        $this->subOrderRepository = $subOrderRepository;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     * @throws AlreadyExistsException
     */
    public function execute()
    {
        $subOrderId = $this->getRequest()->getParam("id");
        try {
            $result = $this->subOrderRepository->setReceivedById($subOrderId);
            if ($result) {
                $this->messageManager->addSuccessMessage(__("Order has been completed."));
            } else {
                $this->messageManager->addErrorMessage(__("Order with ID %1 is not exist.", $subOrderId));
            }
        } catch (InputException $e) {
            $this->messageManager->addErrorMessage(__("Order with ID %1 is not exist.", $subOrderId));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }

        return $this->_redirect($this->_redirect->getRefererUrl());
    }
}
