<?php
/**
 * Class MobileView
 * @package SM\Sales\Controller\Invoice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */


namespace SM\Sales\Controller\Invoice;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use SM\Sales\Model\Data\Invoice\AuthInvoiceLink;

class MobileView extends \Magento\Framework\App\Action\Action
{
    /**
     * @var AuthInvoiceLink
     */
    private $authInvoiceLink;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * MobileView constructor.
     * @param Context $context
     * @param AuthInvoiceLink $authInvoiceLink
     */
    public function __construct(
        Context $context,
        AuthInvoiceLink $authInvoiceLink
    ) {
        parent::__construct($context);
        $this->request = $context->getRequest();
        $this->authInvoiceLink = $authInvoiceLink;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if ($this->authInvoiceLink->authorization($this->request)) {
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $resultPage->getLayout()->getUpdate()->removeHandle('default');
            $resultPage->getConfig()->getTitle()->set(__('Invoice'));
            return $resultPage;
        }

        $result = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        return $result->setController('cms')->forward('noroute');
    }
}
