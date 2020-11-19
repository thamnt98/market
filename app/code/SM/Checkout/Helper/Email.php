<?php

namespace SM\Checkout\Helper;

class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_EMAIL_PREFIX = 'sm_payment/email';
    const EMAIL_TEMPLATE   = '/template';
    const EMAIL_SENDER     = '/sender';

    const EMAIL_TEMPLATE_SYSTEM_FAILED                         = 'payment_system_failed';
    const EMAIL_TEMPLATE_BANK_REJECT                           = 'payment_bank_reject';
    const EMAIL_TEMPLATE_EXPIRED                               = 'payment_expired';
    const EMAIL_TEMPLATE_PAYMENT_SUCCESS_PHYSICAL              = 'payment_success_physical';

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getSystemFailedTemplateId($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_TEMPLATE_SYSTEM_FAILED . self::EMAIL_TEMPLATE, $store);
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getBankRejectTemplateId($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_TEMPLATE_BANK_REJECT . self::EMAIL_TEMPLATE, $store);
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getExpiredTemplateId($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_TEMPLATE_EXPIRED . self::EMAIL_TEMPLATE, $store);
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getPaymentSuccessPhysicalTemplateId($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_TEMPLATE_PAYMENT_SUCCESS_PHYSICAL . self::EMAIL_TEMPLATE, $store);
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getSystemFailedSender($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_TEMPLATE_SYSTEM_FAILED . self::EMAIL_SENDER, $store);
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getBankRejectSender($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_TEMPLATE_BANK_REJECT . self::EMAIL_SENDER, $store);
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getExpiredSender($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_TEMPLATE_EXPIRED . self::EMAIL_SENDER, $store);
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getPaymentSuccessPhysicalSender($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_TEMPLATE_PAYMENT_SUCCESS_PHYSICAL . self::EMAIL_SENDER, $store);
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
