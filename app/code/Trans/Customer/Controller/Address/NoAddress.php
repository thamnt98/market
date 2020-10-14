<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2019 PT CTCORP Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
 
namespace Trans\Customer\Controller\Address;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class NoAddress extends \Magento\Framework\App\Action\Action 
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Require login
     *
     */
    public function execute()
    {
        if (!$this->customerSession->isLoggedIn()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        }
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
