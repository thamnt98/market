<?php

/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Muhammad Randy Surya Perdana <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Plugin\Customer\Controller\Address;

use Magento\Customer\Controller\Address\FormPost as MageFormPost;

/**
 *  Class FormPost
 */
class FormPost
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
     * @param \Trans\Core\Helper\Customer
     */
    protected $customerHelper;

    /**
     * FormPost constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Trans\Core\Helper\Customer $customerHelper
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Customer\Model\Session $customerSession,
        \Trans\Core\Helper\Customer $customerHelper
    ) {
        $this->request = $request;
        $this->customerSession = $customerSession;
        $this->customerHelper = $customerHelper;
    }

    /**
     * phone number validation process
     *
     * @param MageFormPost $subject
     * @param callable $proceed
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundExecute(MageFormPost $subject, callable $proceed)
    {
        $fullName = $this->request->getParam('fullname');
        $name = $this->customerHelper->generateFirstnameLastname($fullName);
        $this->request->setParam('firstname', $name['firstname']);
        $this->request->setParam('lastname', $name['lastname']);
        if ($this->customerSession->isLoggedIn()) {
            $this->request->setParam('default_billing', 1);
            $this->request->setParam('default_shipping', 1);
            if ($this->customerSession->getCustomer()->getDefaultBillingAddress()) {
                $defaultBillingId = $this->customerSession->getCustomer()->getDefaultBillingAddress()->getId();
                if ($defaultBillingId != $this->request->getParam('id')) {
                    $this->request->setParam('default_billing', 0);
                    $this->request->setParam('default_shipping', 0);
                }
            }
        }
        return $proceed();
    }

}
