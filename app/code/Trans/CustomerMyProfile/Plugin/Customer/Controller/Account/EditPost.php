<?php

/**
 * @category Trans
 * @package  Trans_CustomerMyProfile
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\CustomerMyProfile\Plugin\Customer\Controller\Account;

use Magento\Customer\Controller\Account\EditPost as MageEditPost;

/**
 *  Class EditPost
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
     * @param \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $collection;

    /**
     * @param \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    /**
     * @param \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * @param \Trans\Core\Helper\Customer
     */
    protected $customerHelper;

    /**
     * @param \Trans\CustomerMyProfile\Helper\Data
     */
    protected $customerMyProfileHelper;

    /**
     * @param \Trans\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var \Trans\Integration\Model\ResourceModel\IntegrationCustomerCentral\CollectionFactory
     */
    protected $customerCentralCollectionFactory;
    /**
     * @var \Magento\Framework\Controller\Result\Redirect
     */
    protected $resultRedirect;

    /**
     * EditPost constructor.
     *
     * @param \Magento\Framework\Controller\Result\Redirect                                               $resultRedirect
     * @param \Magento\Framework\App\RequestInterface                                                     $request
     * @param \Magento\Framework\Controller\ResultFactory                                                 $resultFactory
     * @param \Magento\Framework\Message\ManagerInterface                                                 $messageManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface                                        $localeDate
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory                            $collection
     * @param \Magento\Customer\Model\Session                                                             $customerSession
     * @param \Magento\Framework\App\Response\RedirectInterface                                           $redirect
     * @param \Trans\Core\Helper\Customer                                                                 $customerHelper
     * @param \Trans\CustomerMyProfile\Helper\Data                                                        $customerMyProfileHelper
     * @param \Trans\Customer\Api\AccountManagementInterface                                              $accountManagement
     * @param \Trans\IntegrationCustomer\Model\ResourceModel\IntegrationCustomerCentral\CollectionFactory $customerCentralCollectionFactory
     */
    public function __construct(
        \Magento\Framework\Controller\Result\Redirect $resultRedirect,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collection,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Trans\Core\Helper\Customer $customerHelper,
        \Trans\CustomerMyProfile\Helper\Data $customerMyProfileHelper,
        \Trans\Customer\Api\AccountManagementInterface $accountManagement,
        \Trans\IntegrationCustomer\Model\ResourceModel\IntegrationCustomerCentral\CollectionFactory $customerCentralCollectionFactory
    ) {
        $this->resultFactory = $resultFactory;
        $this->messageManager = $messageManager;
        $this->localeDate = $localeDate;
        $this->redirect = $redirect;
        $this->request = $request;
        $this->collection = $collection;
        $this->customerSession = $customerSession;
        $this->customerHelper = $customerHelper;
        $this->customerMyProfileHelper = $customerMyProfileHelper;
        $this->accountManagement = $accountManagement;
        $this->customerCentralCollectionFactory = $customerCentralCollectionFactory;
        $this->resultRedirect = $resultRedirect;
    }

    /**
     * set param for edit customer
     *
     * @param MageEditPost $subject
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeExecute(MageEditPost $subject)
    {
        $customer = $this->customerSession->getCustomer();
        $ignore = $this->customerMyProfileHelper->ignoreAttribute(true);
        foreach ($ignore as $attributeCode) {
            $this->request->setParam($attributeCode, $customer->getData($attributeCode));
        }
        if ($this->request->getParam('dob')) {
            $dob = $this->customerSession->getCustomer()->getDob();
            $newDob = $this->request->getParam('dob');
            if ($dob) {
                $dob = date('m/d/Y', strtotime($dob));
            }
            if ($dob != $newDob) {
                $dobChangeLimitCurrent = round($customer->getDobChangeNumber());
                $nextDobChangeCurrent = round($dobChangeLimitCurrent + 1);
                $dobChangeLimit = round($this->customerMyProfileHelper->getDobChangeLimit());

                if ($nextDobChangeCurrent == $dobChangeLimit) {
                    $this->request->setParam('is_disabled_dob', 1);
                }

                if ($dobChangeLimitCurrent >= $dobChangeLimit) {
                    $this->messageManager->addError(__('You have changed the birthday more than the specified number of times. Please contact us!'));
                    $this->request->setParam('dob_change_number', $dobChangeLimitCurrent);
                    $this->request->setParam('dob', $dob);
                } else {
                    $this->request->setParam('dob_change_number', $dobChangeLimitCurrent + 1);
                }
            }
        } else {
            $dobChangeLimitCurrent = round($this->customerSession->getCustomer()->getDobChangeNumber());
            $this->request->setParam('dob_change_number', $dobChangeLimitCurrent + 1);
        }
    }

    /**
     * validation for edit customer
     *
     * @param MageCreatePost $subject
     * @param callable $proceed
     * @return mixed
     */
    public function aroundExecute(MageEditPost $subject, callable $proceed)
    {
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);

        $fullname = $this->request->getParam('fullname');
        if (!$fullname) {
            $this->messageManager->addError(__('"Fullname" is a required value.'));
            $resultRedirect->setPath($this->redirect->getRefererUrl());
            return $resultRedirect;
        }

        if (($this->request->getFiles('profile_picture')['type'] != "") || ($this->request->getFiles('profile_picture')['size'] > 0)) {
            //profile picture validate
            $pp_filesize = $this->request->getFiles('profile_picture')['type'];
            $extensions_file = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $check_file = false;

            if (!in_array($pp_filesize, $extensions_file)) {
                $this->messageManager->addError(__('Picture invalid (Please use format .jpeg .jpg .png .gif).'));
                $resultRedirect->setPath($this->redirect->getRefererUrl());
                return $resultRedirect;
            }

            $maxsize = 1048576;
            $configMaxsize = 1;
            if ($this->customerMyProfileHelper->getMaxsizeProfilePicture() != '') {
                $configMaxsize = $this->customerMyProfileHelper->getMaxsizeProfilePicture();
                $maxsize = 1048576 * $configMaxsize;
            }

            $pp_filesize = $this->request->getFiles('profile_picture')['size'];
            if ($pp_filesize > $maxsize) {
                $this->messageManager->addError(__('Picture size too large (max. ' . $configMaxsize . 'MB).'));
                $resultRedirect->setPath($this->redirect->getRefererUrl());
                return $resultRedirect;
            }
        }

        //check central id is exist or not
        if ($this->customerSession->isLoggedIn()) {
            $centralData = $this->customerCentralCollectionFactory->create()
                ->addFieldToFilter('magento_customer_id', $this->customerSession->getCustomerId())
                ->getFirstItem();
            if ($centralData->getId() && strlen($centralData->getCentralId()) <= 10) {
                $this->messageManager->addError(__('We can\'t save customer information.'));
                $resultRedirect->setPath($this->redirect->getRefererUrl());
                return $resultRedirect;
            }
        }

        return $proceed();
    }

    /**
     * set redirect link after edit customer
     *
     * @param MageCreatePost $subject
     * @return $result
     */
    public function afterExecute(MageEditPost $subject, $result)
    {
        return $this->resultRedirect->setPath('customer/account');
    }
}
