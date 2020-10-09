<?php


namespace SM\Sales\Controller\Invoice;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class View
 * @package SM\Sales\Controller\Invoice
 */
class View extends \Magento\Framework\App\Action\Action
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        // TODO: Implement execute() method.
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getLayout()->getUpdate()->removeHandle('default');
        $resultPage->getConfig()->getTitle()->set(__('Invoice'));
        return $resultPage;
    }
}
