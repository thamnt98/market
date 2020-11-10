<?php

declare(strict_types=1);

namespace SM\Customer\Controller\Popup;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Index extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Index constructor.
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Customer\Model\Session $customerSession
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = ['html' => ''];
        if (!$this->customerSession->isLoggedIn()) {
            $block = '';
            $resultPage = $this->resultPageFactory->create();
            if ($this->getRequest()->getParam('type') == 'login-form') {
                $child = $resultPage->getLayout()->createBlock(\SM\Customer\Block\SocialLogin\Popup\Social::class)
                        ->setTemplate('Mageplaza_SocialLogin::popup/form/authentication/social.phtml');
                $block = $resultPage->getLayout()
                    ->createBlock(\SM\Customer\Block\Form\Login::class)
                    ->setTemplate('SM_Customer::form/login.phtml')
                    ->setChild('login.popup.authentication.social', $child)
                    ->setData([
                        'view_model' => 'SM\Customer\ViewModel\Login'
                    ])
                    ->toHtml();
            } elseif ($this->getRequest()->getParam('type') == 'register-form') {
                $child1 = $resultPage->getLayout()->createBlock(\SM\Customer\Block\SocialLogin\Popup\Social::class)
                    ->setTemplate('Mageplaza_SocialLogin::popup/form/authentication/social.phtml');
                $child2 = $resultPage->getLayout()->createBlock(\SM\Customer\Block\Form\Register::class)
                    ->setTemplate('SM_Customer::terms-conditions.phtml');
                $child3 = $resultPage->getLayout()->createBlock(\SM\Customer\Block\Form\Register::class)
                    ->setTemplate('SM_Customer::privacy-policy.phtml');
                $block = $resultPage->getLayout()
                    ->createBlock(\SM\Customer\Block\Form\Register::class)
                    ->setTemplate('SM_Customer::form/register.phtml')
                    ->setChild('register.popup.authentication.social', $child1)
                    ->setChild('terms.conditions', $child2)
                    ->setChild('privacy.policy', $child3)
                    ->toHtml();
            } elseif ($this->getRequest()->getParam('type') == 'forgot-password-form') {
                $block = $resultPage->getLayout()->createBlock(\SM\Customer\Block\Form\ForgotPassword::class)
                    ->setTemplate('SM_Customer::form/forgot-password/form.phtml')
                    ->toHtml();
                $block .= $resultPage->getLayout()->createBlock(\SM\Customer\Block\Form\ForgotPassword::class)
                    ->setTemplate('SM_Customer::form/forgot-password/confirm.phtml')
                    ->toHtml();
                $block .= $resultPage->getLayout()->createBlock(\SM\Customer\Block\Form\ForgotPassword::class)
                    ->setTemplate('SM_Customer::form/recovery-password/form.phtml')
                    ->toHtml();
            } elseif ($this->getRequest()->getParam('type') == 'recovery-form') {
                $block = $resultPage->getLayout()->createBlock(\SM\Customer\Block\Form\ForgotPassword::class)
                    ->setTemplate('SM_Customer::form/recovery-password/form.phtml')
                    ->toHtml();
            } elseif ($this->getRequest()->getParam('type') == 'social-register-form') {
                $block = $resultPage->getLayout()->createBlock(\SM\Customer\Block\SocialLogin\Popup\Register::class)
                    ->setTemplate('SM_Customer::form/social/register.phtml')
                    ->toHtml();
            } elseif ($this->getRequest()->getParam('type') == 'lock-form') {
                $block = $resultPage->getLayout()->createBlock(\SM\Customer\Block\Form\Register::class)
                    ->setTemplate('SM_Customer::form/lock/form.phtml')
                    ->toHtml();
            } elseif ($this->getRequest()->getParam('type') == 'lock-reset-form') {
                $block = $resultPage->getLayout()->createBlock(\SM\Customer\Block\Form\Register::class)
                    ->setTemplate('SM_Customer::form/lock/reset.phtml')
                    ->toHtml();
            }
            if ($this->getRequest()->getParam('otp') && $this->getRequest()->getParam('otp') == 'false') {
                $block .= $resultPage->getLayout()->createBlock(\SM\Customer\Block\Form\Register::class)
                    ->setTemplate('SM_Customer::form/otp/verification.phtml')
                    ->toHtml();
            }
            $data['html'] = $block;
        }
        $result = $this->jsonResultFactory->create();
        $result->setData($data);
        return $result;
    }
}
