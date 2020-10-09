<?php

declare(strict_types=1);

namespace SM\AndromedaSms\Helper;

use SM\AndromedaApi\Helper\Config as BaseConfig;

class Config extends BaseConfig
{
    const XML_PATH_ANDROMEDA_API_SMS_VERIFICATION_LANGUAGE = 'andromeda_api/sms_verification/language';
    const XML_PATH_ANDROMEDA_API_SMS_VERIFICATION_VERIFICATION_CODE_EXPIRED_IN = 'andromeda_api/sms_verification/verification_code_expired_in';
    const XML_PATH_ANDROMEDA_API_SMS_VERIFICATION_LOCK_AFTER_NUMBER_OF_FAILED = 'andromeda_api/sms_verification/lock_after_number_of_failed';
    const XML_PATH_ANDROMEDA_API_SMS_VERIFICATION_UNLOCK_AFTER_NUMBER_OF_HOURS = 'andromeda_api/sms_verification/unlock_after_number_of_hours';
    const TELEPHONE_ATTRIBUTE_CODE = 'telephone';

    /**
     * @return string
     */
    public function getSmsLanguage(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_ANDROMEDA_API_SMS_VERIFICATION_LANGUAGE
        );
    }

    /**
     * @return int
     */
    public function getVerificationCodeExpiredIn(): int
    {
        $value = (int) $this->scopeConfig->getValue(
            self::XML_PATH_ANDROMEDA_API_SMS_VERIFICATION_VERIFICATION_CODE_EXPIRED_IN
        );
        if ($value == 0 || $value == '' || !$value) {
            $value = 30;
        }
        return $value;
    }

    /**
     * @return int
     */
    public function getNumberOfFailedToLock(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_ANDROMEDA_API_SMS_VERIFICATION_LOCK_AFTER_NUMBER_OF_FAILED
        );
    }

    /**
     * @return int
     */
    public function getNumberOfHoursToUnlock(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_ANDROMEDA_API_SMS_VERIFICATION_UNLOCK_AFTER_NUMBER_OF_HOURS
        );
    }
}
