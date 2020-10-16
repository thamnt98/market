<?php

declare(strict_types=1);

namespace SM\Checkout\Helper;

use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use  Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Config
 * @package SM\Checkout\Helper
 */
class Config extends AbstractHelper
{
    const XML_REDIRECT_TIME_AFTER_PAYMENT_SUCCESS_CC = "sm_payment/credit_card/payment_success_page_time";

    /**
     * @var StoreManagerInterface
     */
    protected $storeConfig;

    /**
     * @var CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * Config constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param StoreManagerInterface $storeConfig
     * @param CurrencyFactory $currencyFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        StoreManagerInterface $storeConfig,
        CurrencyFactory $currencyFactory
    )
    {
        $this->storeConfig = $storeConfig;
        $this->currencyFactory = $currencyFactory;
        parent::__construct($context);
    }

    /**
     * @param mixed $storeId
     * @return int
     */
    public function getAddressLimit($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(
            'sm_customer/customer_address_limit/limit',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param mixed $storeId
     * @return array
     */
    public function getDateTimeConfig($storeId = null)
    {
        $data = [];
        $data['date_limit'] = (int)$this->scopeConfig->getValue(
            'carriers/store_pickup/date_limit',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $data['today_time_start'] = (int)$this->scopeConfig->getValue(
            'carriers/store_pickup/today_time_start',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $data['nextday_time_start'] = (int)$this->scopeConfig->getValue(
            'carriers/store_pickup/nextday_time_start',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $data['time_end'] = (int)$this->scopeConfig->getValue(
            'carriers/store_pickup/time_end',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if ($data['time_end'] > 24) {
            $data['time_end'] = 24;
        }
        return $data;
    }

    /**
     * @param null $storeId
     * @return array
     */
    public function getDeliveryDateTimeConfig($storeId = null)
    {
        $data['from_date'] = 1;
        $data['to_date'] = 7;
        $data['time_start'] = (int)$this->scopeConfig->getValue(
            'carriers/store_pickup/nextday_time_start',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $data['time_end'] = (int)$this->scopeConfig->getValue(
            'carriers/store_pickup/time_end',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        return $data;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrencySymbol()
    {
        $currentCurrency = $this->storeConfig->getStore()->getCurrentCurrencyCode();
        $currency = $this->currencyFactory->create()->load($currentCurrency);
        return $currency->getCurrencySymbol();
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getWeightUnit($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'general/locale/weight_unit',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     */
    public function getSecretKey($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'allowloc/generaltopsetting/secretkey',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getTimeRedirect()
    {
        return $this->scopeConfig->getValue(
            self::XML_REDIRECT_TIME_AFTER_PAYMENT_SUCCESS_CC,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $code
     * @return mixed|string
     */
    public function getPaymentDescription($code)
    {
        switch ($code) {
            case 'sprint_allbankfull_cc':
                $description = $this->scopeConfig->getValue('payment/sprint/sprint_cc_payment/'. $code .'/description');
                break;
            case 'sprint_allbank_debit':
                $description = $this->scopeConfig->getValue('payment/sprint/sprint_cc_payment/'. $code .'/description');
                break;
            case 'sprint_bca_cc':
                $description = $this->scopeConfig->getValue('payment/sprint/sprint_cc_payment/'. $code .'/description');
                break;
            case 'sprint_mega_cc':
                $description = $this->scopeConfig->getValue('payment/sprint/sprint_cc_payment/'. $code .'/description');
                break;
            case 'sprint_bca_va':
                $description = $this->scopeConfig->getValue('payment/sprint/sprint_channel/'. $code .'/description');
                break;
            case 'sprint_permata_va':
                $description = $this->scopeConfig->getValue('payment/sprint/sprint_channel/'. $code .'/description');
                break;

            default:
                $description = '';
                break;
        }
        return $description;
    }
    /**
     * @param $code
     * @return mixed|string
     */
    public function getPaymentTooltipDescription($code)
    {
        $description = '';
        if ($code) {
            return $this->scopeConfig->getValue('payment/sprint/sprint_cc_payment/'. $code .'/tooltip_description');
        }
        return $description;
    }

    /**
     * @return mixed
     */
    public function isActiveFulfillmentStore()
    {
        return $this->scopeConfig->getValue(
            'sm_checkout_fulfillment/general/active',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getDeliveryAreaLink()
    {
        return $this->scopeConfig->getValue(
            'sm_checkout_fulfillment/general/link',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $code
     * @return mixed|string
     */
    public function getPaymentLogo($code)
    {
        return $this->scopeConfig->getValue('payment/'. $code .'/logo');
    }

    /**
     * @return bool
     */
    public function showOrderSummary()
    {
        return $this->scopeConfig->getValue(
            'sm_checkout/checkout_order_summary/active',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return int
     */
    public function getTermAndConditionFaq()
    {
        return $this->scopeConfig->getValue(
            'sm_help/payment_page/terms_conditions_mobile'
        );
    }
}
