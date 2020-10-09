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

namespace Trans\Customer\Block\Account;

/**
 * Class CustomerSession
 */
class CustomerSession extends \Magento\Framework\View\Element\Template
{
    
    protected $customerSession;
    
     /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->resultFactory = $resultFactory;
        parent::__construct($context, $data);
    }
     
    public function getLoggedinCustomerId() {
        if (!$this->customerSession->create()->isLoggedIn()) {
                $resultRedirects = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
                return $resultRedirects;
            }
        }

    public function getSitesLogin()
    {   
        $url  = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
        $loginurl = $this->getUrl('customer/account/login', array('referer' => base64_encode($url)));
        return $loginurl;
    }
}
