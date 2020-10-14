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

namespace Trans\Customer\Plugin\Customer\Model;

use Magento\Customer\Model\AccountManagement as MageAccountManagement;
use Magento\Framework\Exception\State\UserLockedException;

/**
 * Class AccountManagement
 */
class AccountManagement
{
    /**
     * @param \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @param \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * AccountManagement constructor.
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->resultFactory = $resultFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * redirect to recovery page if account locked
     *
     * @param MageAccountManagement $subject
     * @param callable $proceed
     * @param string $username
     * @param string $password
     */
    public function aroundAuthenticate(MageAccountManagement $subject, callable $proceed, $username, $password)
    {
        try {
            return $proceed($username, $password);
        } catch (UserLockedException $err) {
            $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $this->messageManager->addError(__($err->getMessage()));
            $resultRedirect->setPath('customer/index/recovery');
            return $resultRedirect;
        }
    }
}

