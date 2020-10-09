<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Plugin\SM\Customer\Controller\Trans;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use SM\AndromedaSms\Helper\Config;
use SM\AndromedaSms\Model\SmsVerification\Validator;
use SM\Customer\Controller\Trans\CreateAccount as CreateAccountController;

class CreateAccount
{
    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * CreateAccount constructor.
     * @param Validator $validator
     * @param Context $context
     */
    public function __construct(
        Validator $validator,
        Context $context
    ) {
        $this->validator = $validator;
        $this->messageManager = $context->getMessageManager();
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
    }

    /**
     * @param CreateAccountController $subject
     * @param \Closure $proceed
     * @return Redirect
     */
    public function aroundExecute(CreateAccountController $subject, \Closure $proceed): Redirect
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/plugin-sms.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        if ($subject->getRequest()->isPost()
            && $phoneNumber = $subject->getRequest()->getParam(Config::TELEPHONE_ATTRIBUTE_CODE)) {
            try {
                $this->validator->validateVerified($phoneNumber);
            } catch (LocalizedException $exception) {
                $logger->info($exception->getMessage());
                $this->messageManager->addErrorMessage($exception->getMessage());

                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('');
                return $resultRedirect;
            }
        }
        return $proceed();
    }
}
