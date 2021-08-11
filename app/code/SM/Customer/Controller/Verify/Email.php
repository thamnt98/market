<?php

declare(strict_types=1);

namespace SM\Customer\Controller\Verify;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use SM\Customer\Helper\Config;
use SM\Customer\Model\Customer\Validator;
use SM\Customer\Model\Email\Sender as EmailSender;

class Email extends Action
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $repository;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var EmailSender
     */
    protected $emailSender;

    /**
     * Email constructor.
     * @param Context $context
     * @param CustomerRepositoryInterface $repository
     * @param Validator $validator
     * @param EmailSender $sender
     */
    public function __construct(
        Context $context,
        CustomerRepositoryInterface $repository,
        Validator $validator,
        EmailSender $sender
    ) {
        parent::__construct($context);
        $this->repository = $repository;
        $this->validator = $validator;
        $this->emailSender = $sender;
    }

    /**
     * @return Redirect
     */
    public function execute(): Redirect
    {
        try {
            $email = $this->getRequest()->getParam(Config::EMAIL_ATTRIBUTE_CODE);
            $this->validator->validateEmail($email);

            $token = $this->getRequest()->getParam(Config::TOKEN_FIELD_NAME);
            $customerData = $this->repository->get($email);

            $this->validator->validateToken($customerData, $token);

            $customerData->setCustomAttribute(Config::IS_VERIFIED_EMAIL_ATTRIBUTE_CODE, 1);

            $savedCustomerData = $this->repository->save($customerData);
            $this->emailSender->sendRegistrationSuccessEmail($savedCustomerData);

            $this->messageManager->addSuccessMessage(__('Your email has been verified.'));
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('');
        return $resultRedirect;
    }
}