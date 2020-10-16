<?php

declare(strict_types=1);

namespace SM\Customer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 * @package SM\Customer\Helper
 */
class Config extends AbstractHelper
{
    const MODULE_NAME = 'SM_Customer';
    const EMAIL_ATTRIBUTE_CODE = 'email';
    const PHONE_ATTRIBUTE_CODE = 'telephone';
    const TOKEN_FIELD_NAME = 'token';
    const IS_VERIFIED_EMAIL_ATTRIBUTE_CODE = 'is_verified_email';
    const IS_DISABLED_DATE_OF_BIRTH = 'is_disabled_dob';
    const XML_PATH_CUSTOMER_VERIFY_EMAIL_TEMPLATE = 'sm_customer/verify_email/template';
    const XML_PATH_CUSTOMER_VERIFY_EMAIL_SENDER = 'sm_customer/verify_email/sender';
    const XML_PATH_CUSTOMER_REGISTRATION_SUCCESS_EMAIL_TEMPLATE = 'sm_customer/registration_success_email/template';
    const XML_PATH_CUSTOMER_REGISTRATION_SUCCESS_EMAIL_SENDER = 'sm_customer/registration_success_email/sender';
    const XML_PATH_CUSTOMER_CHANGE_TELEPHONE_EMAIL_TEMPLATE = 'sm_customer/change_telephone_notification/template';
    const XML_PATH_CUSTOMER_CHANGE_TELEPHONE_EMAIL_SENDER = 'sm_customer/change_telephone_notification/sender';
    const XML_PATH_CUSTOMER_CHANGE_EMAIL_TEMPLATE = 'sm_customer/change_email_notification/template';
    const XML_PATH_CUSTOMER_CHANGE_EMAIL_SENDER = 'sm_customer/change_email_notification/sender';
    const XML_PATH_CUSTOMER_CHANGE_PERSONAL_INFORMATION_EMAIL_TEMPLATE = 'sm_customer/change_personal_information_notification/template';
    const XML_PATH_CUSTOMER_CHANGE_PERSONAL_INFORMATION_EMAIL_SENDER = 'sm_customer/change_personal_information_notification/sender';
    const XML_PATH_CUSTOMER_RECOVERY_EMAIL_TEMPLATE = 'sm_customer/recovery/template';
    const XML_PATH_CUSTOMER_LOCK_EMAIL_TEMPLATE = 'sm_customer/recovery/lock';
    const MAX_FAILURES_PATH = 'sm_customer/recovery/lockout_failures';
    const XML_PATH_CUSTOMER_RECOVERY_EMAIL_SENDER = 'sm_customer/recovery/sender';
    const XML_PATH_CUSTOMER_RECOVERY_TELEPHONE_SENDER = 'sm_customer/recovery/telephone';
    const XML_PATH_CUSTOMER_CHANGE_PASSWORD_NOTIFICATION_EMAIL_TEMPLATE = 'sm_customer/change_password_notification/template';
    const XML_PATH_CUSTOMER_CHANGE_PASSWORD_NOTIFICATION_EMAIL_SENDER = 'sm_customer/change_password_notification/sender';
    const IS_EDIT_ADDRESS_ATTRIBUTE_CODE = 'is_edit_address';

    const XML_PATH_TERMS_CONDITIONS = 'sm_help/terms_privacy/terms_conditions';
    const XML_PATH_PRIVACY_POLICY = 'sm_help/terms_privacy/privacy_policy';

    /**
     * @param $telephone
     * @return string|string[]|null
     */
    public function trimTelephone($telephone)
    {
        $telephone = preg_replace("/^(^\+628|^628|^08|^8)/", '', $telephone);
        return '628' . $telephone;
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getVerifyEmailTemplate(int $storeId): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_VERIFY_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return string
     */
    public function getVerifyEmailSender(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_VERIFY_EMAIL_SENDER
        );
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getRegistrationSuccessEmailTemplate(int $storeId): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_REGISTRATION_SUCCESS_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return string
     */
    public function getRegistrationSuccessEmailSender(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_REGISTRATION_SUCCESS_EMAIL_SENDER
        );
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getChangeTelephoneTemplate(int $storeId): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_CHANGE_TELEPHONE_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return string
     */
    public function getChangeTelephoneSender(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_CHANGE_TELEPHONE_EMAIL_SENDER
        );
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getChangePassWordTemplate(int $storeId): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_CHANGE_PASSWORD_NOTIFICATION_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return string
     */
    public function getChangePassWordSender(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_CHANGE_PASSWORD_NOTIFICATION_EMAIL_SENDER
        );
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getChangeEmailTemplate(int $storeId): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_CHANGE_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return string
     */
    public function getChangeEmailSender(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_CHANGE_EMAIL_SENDER
        );
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getChangePersonalInformationTemplate(int $storeId): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_CHANGE_PERSONAL_INFORMATION_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return string
     */
    public function getChangePersonalInformationSender(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_CHANGE_PERSONAL_INFORMATION_EMAIL_SENDER
        );
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getRecoveryTemplate(int $storeId): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_RECOVERY_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getLockTemplate(int $storeId): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_LOCK_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int $storeId
     * @return int
     */
    public function getMaxFailures(int $storeId)
    {
        return (int) $this->scopeConfig->getValue(
            self::MAX_FAILURES_PATH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return string
     */
    public function getRecoverySender(int $storeId): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_RECOVERY_EMAIL_SENDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return string
     */
    public function getSenderPhone(int $storeId): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_RECOVERY_TELEPHONE_SENDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return mixed
     */
    public function getTermsConditions()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TERMS_CONDITIONS,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getPrivacyPolicy()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PRIVACY_POLICY,
            ScopeInterface::SCOPE_STORE
        );
    }
}
