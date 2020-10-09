<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: June, 17 2020
 * Time: 10:16 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Helper\Generate;

class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_EMAIL_PREFIX = 'sm_notification/email';

    const EMAIL_TEMPLATE_SYSTEM_FAILED                           = 'payment_system_failed';
    const EMAIL_TEMPLATE_SYSTEM_FAILED_DIGITAL                   = 'payment_system_failed_digital';
    const EMAIL_TEMPLATE_BANK_REJECT                             = 'payment_bank_reject';
    const EMAIL_TEMPLATE_EXPIRED                                 = 'payment_expired';
    const EMAIL_TEMPLATE_PAYMENT_SUCCESS_DIGITAL_TOP_UP          = 'payment_success_top_up';
    const EMAIL_TEMPLATE_PAYMENT_SUCCESS_DIGITAL_MOBILE_PACKAGE  = 'payment_success_mobile_package';
    const EMAIL_TEMPLATE_PAYMENT_SUCCESS_DIGITAL_MOBILE_ROAMING  = 'payment_success_roaming';
    const EMAIL_TEMPLATE_PAYMENT_SUCCESS_DIGITAL_PLN_TOKEN       = 'payment_success_pln_token';
    const EMAIL_TEMPLATE_PAYMENT_SUCCESS_DIGITAL_PLN_BILL        = 'payment_success_pln_bill';
    const EMAIL_TEMPLATE_PAYMENT_SUCCESS_DIGITAL_MOBILE_POSTPAID = 'payment_success_mobile_postpaid';
    const EMAIL_TEMPLATE_PAYMENT_SUCCESS_PHYSICAL                = 'payment_success_physical';

    /**
     * @var \Magento\Email\Model\TemplateFactory
     */
    protected $emailTemplateFactory;

    public function __construct(
        \Magento\Email\Model\TemplateFactory $emailTemplateFactory,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->emailTemplateFactory = $emailTemplateFactory;
    }

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
    public function getPaymentSuccessDigitalTopUpTemplateId($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_TEMPLATE_PAYMENT_SUCCESS_DIGITAL_TOP_UP, $store);
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getPaymentSuccessDigitalMobilePackageTemplateId($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_TEMPLATE_PAYMENT_SUCCESS_DIGITAL_MOBILE_PACKAGE, $store);
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getPaymentSuccessDigitalMobileRoamingTemplateId($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_TEMPLATE_PAYMENT_SUCCESS_DIGITAL_MOBILE_ROAMING, $store);
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getPaymentSuccessDigitalPlnTokenTemplateId($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_TEMPLATE_PAYMENT_SUCCESS_DIGITAL_PLN_TOKEN, $store);
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getPaymentSuccessDigitalPlnBillTemplateId($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_TEMPLATE_PAYMENT_SUCCESS_DIGITAL_PLN_BILL, $store);
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getPaymentSuccessDigitalMobilePostpaidTemplateId($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_TEMPLATE_PAYMENT_SUCCESS_DIGITAL_MOBILE_POSTPAID, $store);
    }

    /**
     * @param $store
     *
     * @return mixed
     */
    public function getPaymentSystemFailedDigitalTemplateId($store = null)
    {
        return $this->getConfigEmailTemplateID(self::EMAIL_TEMPLATE_SYSTEM_FAILED_DIGITAL, $store);
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

    /**
     * @param $id
     *
     * @return \Magento\Email\Model\Template|null
     */
    public function getEmailTemplateById($id)
    {
        /** @var \Magento\Email\Model\Template $template */
        $template = $this->emailTemplateFactory->create()->load($id);

        if ($template->getId()) {
            return $template;
        } else {
            return null;
        }
    }
}
