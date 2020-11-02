<?php


namespace SM\Sales\Controller\Invoice;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class View
 * @package SM\Sales\Controller\Invoice
 */
class View extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getLayout()->getUpdate()->removeHandle('default');
        $resultPage->getConfig()->getTitle()->set(__('Invoice'));
        return $resultPage;
    }
}
