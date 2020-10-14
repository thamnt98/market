<?php

/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Original Muhammad Randy Surya Perdana <muhammad.randy@transdigital.co.id>
 * @author   Another  Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Plugin\Customer\Controller\Account;

use Magento\Customer\Controller\Account\EditPost as MageEditPost;

/**
 * Class EditPost
 * @package Trans\Customer\Plugin\Customer\Controller\Account
 */
class EditPost
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $collection;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Trans\Core\Helper\Customer
     */
    protected $customerHelper;

    /**
     * EditPost constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collection
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Trans\Core\Helper\Customer $customerHelper
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collection,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Trans\Core\Helper\Customer $customerHelper
    )
    {
        $this->request = $request;
        $this->resultFactory = $resultFactory;
        $this->messageManager = $messageManager;
        $this->collection = $collection;
        $this->customerSession = $customerSession;
        $this->redirect = $redirect;
        $this->customerHelper = $customerHelper;
    }

    /**
     * phone number validation process
     *
     * @param MageCreatePost $subject
     * @param callable $proceed
     * @return mixed
     */
    public function aroundExecute(MageEditPost $subject, callable $proceed)
    {
        $customer = $this->customerSession->getCustomer();
        if (!$this->request->getParam('submit_edit_info')) {
            $firstName = $customer->getFirstname();
            $lastName = $customer->getLastname();
            $names = [$firstName, $lastName];
            $fullName = $this->customerHelper->generateFullnameByArray($names);
            if (!$this->request->getParam('fullname')) {
                $this->request->setParam('fullname', $fullName);
            }
        }

        $phone = $this->request->getParam('telephone');
        $customerId = $customer->getId();
        $customerData = $this->collection->create()
            ->addFieldToFilter('telephone', $phone)
            ->addFieldToFilter('entity_id', ['neq' => $customerId])
            ->getFirstItem();

        //check data telephone is already exist or not
        if ($customerData->getId()) {
            //get message error when data customer telephone already exist
            $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $this->messageManager->addError(__('Phone number already exist'));
            $resultRedirect->setPath($this->redirect->getRefererUrl());

            return $resultRedirect;
        }

        $fullName = $this->request->getParam('fullname');
        $name = $this->customerHelper->generateFirstnameLastname($fullName);
        $this->request->setParam('firstname', $name['firstname']);
        $this->request->setParam('lastname', $name['lastname']);

        return $proceed();
    }

}
