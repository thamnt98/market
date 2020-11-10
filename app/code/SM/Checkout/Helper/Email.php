<?php

namespace SM\Checkout\Helper;

class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_EMAIL_PREFIX = 'sm_payment/email';

    const EMAIL_TEMPLATE_SYSTEM_FAILED                           = 'payment_system_failed_template';
    const EMAIL_TEMPLATE_BANK_REJECT                             = 'payment_bank_reject_template';
    const EMAIL_TEMPLATE_EXPIRED                                 = 'payment_expired_template';
    const EMAIL_TEMPLATE_PAYMENT_SUCCESS_PHYSICAL                = 'payment_success_physical_template';

    const EMAIL_SENDER_SYSTEM_FAILED                           = 'payment_system_failed_sender';
    const EMAIL_SENDER_BANK_REJECT                             = 'payment_bank_reject_sender';
    const EMAIL_SENDER_EXPIRED                                 = 'payment_expired_sender';
    const EMAIL_SENDER_PAYMENT_SUCCESS_PHYSICAL                = 'payment_success_physical_sender';

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getSystemFailedTemplateId($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_TEMPLATE_SYSTEM_FAILED, $store);
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getBankRejectTemplateId($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_TEMPLATE_BANK_REJECT, $store);
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getExpiredTemplateId($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_TEMPLATE_EXPIRED, $store);
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getPaymentSuccessPhysicalTemplateId($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_TEMPLATE_PAYMENT_SUCCESS_PHYSICAL, $store);
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getSystemFailedSender($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_SENDER_SYSTEM_FAILED, $store);
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getBankRejectSender($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_SENDER_BANK_REJECT, $store);
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getExpiredSender($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_SENDER_EXPIRED, $store);
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getPaymentSuccessPhysicalSender($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_SENDER_PAYMENT_SUCCESS_PHYSICAL, $store);
    }

    /**
     * @param      $type
     * @param null $store
     *
     * @return mixed
     */
    public function getConfigEmailTemplateID($type, $store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_EMAIL_PREFIX . '/' . $type,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
