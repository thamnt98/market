<?php

declare(strict_types=1);

namespace SM\Customer\Model\Email;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Math\Random;
use Magento\Framework\UrlInterface;
use SM\Customer\Helper\Config;
use SM\Email\Model\Email\Sender as EmailSender;
use Magento\Framework\Mail\Template\SenderResolverInterface;

class Sender
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Preparator
     */
    protected $preparator;

    /**
     * @var EmailSender
     */
    protected $emailSender;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Random
     */
    protected $mathRandom;

    /**
     * @var AccountManagement
     */
    protected $accountManagement;

    /**
     * @var SenderResolverInterface
     */
    protected $senderResolver;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * Sender constructor.
     * @param Config $config
     * @param Preparator $preparator
     * @param EmailSender $emailSender
     * @param UrlInterface $urlBuilder
     * @param Random $mathRandom
     * @param AccountManagement $accountManagement
     * @param SenderResolverInterface $senderResolver
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     */
    public function __construct(
        Config $config,
        Preparator $preparator,
        EmailSender $emailSender,
        UrlInterface $urlBuilder,
        Random $mathRandom,
        AccountManagement $accountManagement,
        SenderResolverInterface $senderResolver,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        $this->config = $config;
        $this->preparator = $preparator;
        $this->emailSender = $emailSender;
        $this->urlBuilder = $urlBuilder;
        $this->mathRandom = $mathRandom;
        $this->accountManagement = $accountManagement;
        $this->senderResolver = $senderResolver;
        $this->timezone = $timezone;
    }

    /**
     * @param CustomerInterface $customerData
     * @throws LocalizedException
     * @throws MailException
     */
    public function sendVerifyEmail(CustomerInterface $customerData): void
    {
        $templateId = $this->config->getVerifyEmailTemplate((int) $customerData->getStoreId());
        if (!$templateId) {
            throw new LocalizedException(__('Verify Email Template does not exist'));
        }

        $sender = $this->config->getVerifyEmailSender();
        $templateVars = [
            'name' => $customerData->getFirstname(),
            'email' => $customerData->getEmail(),
            'verify_email_url' => $this->preparator->getVerifyEmailUrl($customerData),
        ];

        $this->emailSender->send(
            $templateId,
            $sender,
            $customerData->getEmail(),
            $this->preparator->getCustomerName($customerData),
            $templateVars,
            (int) $customerData->getStoreId()
        );
    }

    /**
     * @param CustomerInterface $customerData
     * @throws LocalizedException
     * @throws MailException
     */
    public function sendRegistrationSuccessEmail(CustomerInterface $customerData): void
    {
        $templateId = $this->config->getRegistrationSuccessEmailTemplate((int) $customerData->getStoreId());
        if (!$templateId) {
            throw new LocalizedException(__('Registration Success Email Template does not exist'));
        }

        $sender = $this->config->getRegistrationSuccessEmailSender();
        $templateVars = [
            'name' => $customerData->getFirstname(),
            'email' => $customerData->getEmail(),
        ];

        $this->emailSender->send(
            $templateId,
            $sender,
            $customerData->getEmail(),
            $this->preparator->getCustomerName($customerData),
            $templateVars,
            (int) $customerData->getStoreId()
        );
    }

    /**
     * @param CustomerInterface $customerData
     * @throws LocalizedException
     * @throws MailException
     */
    public function sendChangeTelephoneEmail(CustomerInterface $customerData): void
    {
        $templateId = $this->config->getChangeTelephoneTemplate((int) $customerData->getStoreId());
        if (!$templateId) {
            throw new LocalizedException(__('Change Telephone Notification Template does not exist'));
        }
        $telephone = '';
        if ($customerData->getCustomAttribute('telephone')) {
            $telephone = $customerData->getCustomAttribute('telephone')->getValue();
        }
        $sender = $this->config->getChangeTelephoneSender();
        $templateVars = [
            'name' => $customerData->getFirstname(),
            'telephone' => $telephone
        ];

        $this->emailSender->send(
            $templateId,
            $sender,
            $customerData->getEmail(),
            $this->preparator->getCustomerName($customerData),
            $templateVars,
            (int) $customerData->getStoreId()
        );
    }

    /**
     * @param CustomerInterface $customerData
     * @param string $type
     * @throws LocalizedException
     * @throws MailException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function sendRecoveryEmail(CustomerInterface $customerData, $type): void
    {
        if ($type == 'lock') {
            $templateId = $this->config->getLockTemplate((int) $customerData->getStoreId());
        } elseif ($type == 'recovery') {
            $templateId = $this->config->getRecoveryTemplate((int) $customerData->getStoreId());
        } else {
            throw new LocalizedException(__('Permission!'));
        }
        if (!$templateId) {
            throw new LocalizedException(__('Recovery Template does not exist'));
        }
        $newPasswordToken = $this->mathRandom->getUniqueHash();
        $this->accountManagement->changeResetPasswordLinkToken($customerData, $newPasswordToken);
        $customerName = $this->preparator->getCustomerName($customerData);
        $sender = $this->config->getRecoverySender((int) $customerData->getStoreId());
        $senderData = $this->senderResolver->resolve($sender, (int) $customerData->getStoreId());
        $senderName = '';
        if (isset($senderData['email'])) {
            $senderName = '/' . $senderData['email'];
        }
        $templateVars = [
            'name' => $this->preparator->getCustomerName($customerData),
            'token' => $this->urlBuilder->getBaseUrl() . '?recoverytoken=' . $newPasswordToken . '&email=' . urlencode($customerData->getEmail()) . '&name=' . urlencode($customerName)
        ];
        if ($type == 'lock') {
            $templateVars['limit'] = $this->config->getMaxFailures((int) $customerData->getStoreId());
            $templateVars['contact'] = $this->config->getSenderPhone((int) $customerData->getStoreId()) . $senderName;
        }
        $this->emailSender->send(
            $templateId,
            $sender,
            $customerData->getEmail(),
            $customerName,
            $templateVars,
            (int) $customerData->getStoreId()
        );
    }

    /**
     * @param CustomerInterface $customerData
     * @throws LocalizedException
     * @throws MailException
     */
    public function sendChangePersonalInformation(CustomerInterface $customerData): void
    {
        $templateId = $this->config->getChangePersonalInformationTemplate((int) $customerData->getStoreId());
        if (!$templateId) {
            throw new LocalizedException(__('Change Personal Information Template does not exist'));
        }
        $telephone = '';
        if ($customerData->getCustomAttribute('telephone')) {
            $telephone = $customerData->getCustomAttribute('telephone')->getValue();
        }
        $sender = $this->config->getChangePersonalInformationSender();
        $templateVars = [
            'name' => $customerData->getFirstname(),
            'telephone' => $telephone
        ];

        $this->emailSender->send(
            $templateId,
            $sender,
            $customerData->getEmail(),
            $this->preparator->getCustomerName($customerData),
            $templateVars,
            (int) $customerData->getStoreId()
        );
    }

    /**
     * @param CustomerInterface $customerData
     * @throws LocalizedException
     * @throws MailException
     */
    public function sendChangeEmail(CustomerInterface $customerData): void
    {
        $templateId = $this->config->getChangeEmailTemplate((int) $customerData->getStoreId());
        if (!$templateId) {
            throw new LocalizedException(__('Change Email Template does not exist'));
        }

        $sender = $this->config->getChangeEmailSender();
        $date = $this->timezone->date();
        $templateVars = [
            'name' => $customerData->getFirstname(),
            'email' => $customerData->getEmail(),
            'verify_email_url' => $this->preparator->getVerifyEmailUrl($customerData),
            'day' => $date->format('l'),
            'date' => $date->format('d/m/yy'),
            'time' => $date->format('H.i A')
        ];

        $this->emailSender->send(
            $templateId,
            $sender,
            $customerData->getEmail(),
            $this->preparator->getCustomerName($customerData),
            $templateVars,
            (int) $customerData->getStoreId()
        );
    }

    /**
     * @param CustomerInterface $customerData
     * @throws LocalizedException
     * @throws MailException
     */
    public function sendChangePassWord(CustomerInterface $customerData): void
    {
        $templateId = $this->config->getChangePassWordTemplate((int) $customerData->getStoreId());
        if (!$templateId) {
            throw new LocalizedException(__('Change Email Template does not exist'));
        }

        $sender = $this->config->getChangePassWordSender();
        $templateVars = [
            'name' => $customerData->getFirstname(),
            'email' => $customerData->getEmail()
        ];

        $this->emailSender->send(
            $templateId,
            $sender,
            $customerData->getEmail(),
            $this->preparator->getCustomerName($customerData),
            $templateVars,
            (int) $customerData->getStoreId()
        );
    }
}
