<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Controller\Recovery;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\Exception\StateException;

/**
 * Class Email
 */
class Email extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $session;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Trans\Customer\Logger\Logger
     */
    protected $logger;

    /**
     * @var \Trans\Customer\Block\Account\Locked
     */
    protected $blockLocked;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Session\SessionManagerInterface $session
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Trans\Customer\Logger\Logger $logger
     * @param \Trans\Customer\Block\Account\Locked $blockLocked
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Framework\Registry $coreRegistry,
        \Trans\Customer\Logger\Logger $logger,
        \Trans\Customer\Block\Account\Locked $blockLocked
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->session = $session;
        $this->coreRegistry = $coreRegistry;
        $this->customerSession = $customerSession;
        $this->logger = $logger;
        $this->blockLocked = $blockLocked;
        parent::__construct($context);
    }

    /**
     * Customer Locked Account
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if(!$this->customerSession->getLockedAcc()) {
            $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath($this->_redirect->getRefererUrl());
                          
            return $resultRedirect;
        }
        
        $this->logger->info('----Start ' . __CLASS__);
        
        $customer = $this->customerSession->getLockedAcc();
        $this->coreRegistry->register('email', $customer['email']);

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setHeader('Authentication-Required', 'true');
        $resultPage->getConfig()->getTitle()->set('Recovery Account');

        $this->logger->info('----End ' . __CLASS__);

        return $resultPage;
    }
}
