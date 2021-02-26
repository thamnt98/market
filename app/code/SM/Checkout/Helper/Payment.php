<?php


namespace SM\Checkout\Helper;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\Helper\Data as PricingData;
use Magento\Quote\Model\Quote\Address;
use Trans\Sprint\Helper\Config;

class Payment
{
    const CREDIT          = [
        'sprint_allbankfull_cc',
        'sprint_mega_cc',
        'trans_mepay_cc',
        'trans_mepay_allbank_cc'
    ];
    const VIRTUAL_ACCOUNT = [
        'sprint_bca_va',
        'trans_mepay_va'
    ];
    const INSTALLMENT     = [
        'sprint_bca_cc'
    ];
    const DEBIT           = [];
    const OVO             = [];
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magento\Cms\Api\BlockRepositoryInterface
     */
    protected $blockRepository;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var Config
     */
    protected $configHelper;
    /**
     * @var PricingData
     */
    protected $pricingData;
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;
    /**
     * @var Session
     */
    protected $sessionCheckout;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\PaymentMethods\HowToPayInterfaceFactory
     */
    private $howToPayFactory;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepository,
        Session $sessionCheckout,
        Config $configHelper,
        PricingData $pricingData,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SM\Checkout\Api\Data\Checkout\PaymentMethods\HowToPayInterfaceFactory $howToPayFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->blockRepository = $blockRepository;
        $this->storeManager = $storeManager;
        $this->configHelper = $configHelper;
        $this->pricingData = $pricingData;
        $this->json = $json;
        $this->sessionCheckout = $sessionCheckout;
        $this->howToPayFactory = $howToPayFactory;
    }

    public function getBlockHowToPay($paymentMethod = '', $toArray = false)
    {
        $blockId = 'how-to-pay';
        $howToPay = [];
        if ($paymentMethod) {
            $blockIdTmp = $blockId;
            for ($i = 1; $i < 10; $i++) {
                $blockId = $blockIdTmp . '-' . $paymentMethod . '-' . $i;
                try {
                    $block = $this->blockRepository->getById($blockId);
                    $content = $block->getContent();
                    if (empty($content)) {
                        continue;
                    }

                    if ($toArray) {
                        $content = preg_split('/\r\n|\r|\n/', strip_tags($content));
                    }
                    $howToPayItem = $this->howToPayFactory->create();
                    $howToPayItem->setBlockTitle($block->getTitle());
                    $howToPayItem->setBlockContent($content);
                    $howToPay[] = $howToPayItem;
                } catch (LocalizedException $e) {
                    continue;
                }
            }
        }

        return $howToPay;
    }

    public function getBlockPaymentNote($paymentMethod, $toArray = false)
    {
        $blockId = 'payment_note';
        $blockId .= '_' . $paymentMethod;

        try {
            $content = $this->blockRepository->getById($blockId)->getContent();
        } catch (LocalizedException $e) {
            return null;
        }

        if (empty($content)) {
            return null;
        }

        if ($toArray) {
            return  preg_split('/\r\n|\r|\n/', strip_tags($content));
        }

        return $content;
    }

    public function getLogoPayment($paymentMethod, $withDomain = false)
    {
        $this->scopeConfig->getValue('payment/' . $paymentMethod . '/logo');
        if (empty($this->scopeConfig->getValue('payment/' . $paymentMethod . '/logo'))) {
            return null;
        }
        $url = '';
        if ($withDomain) {
            $url = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        }
        $directory = 'logo/paymentmethod/';
        return $url . $directory . $this->scopeConfig->getValue('payment/' . $paymentMethod . '/logo');
    }

    public function isSprintPaymentMethod($paymentMethod)
    {
        return in_array($paymentMethod, $this->getSprintPaymentMethods());
    }

    public function isVirtualAccount($paymentMethod)
    {
        return in_array($paymentMethod, $this->getVirtualAccountMethods());
    }

    public function isCredit($paymentMethod)
    {
        return in_array($paymentMethod, $this->getCreditMethods());
    }

    public function isDebit($paymentMethod)
    {
        return in_array($paymentMethod, $this->getDebitMethods());
    }

    public function isInstallment($paymentMethod)
    {
        return in_array($paymentMethod, $this->getInstallmentMethods());
    }

    public function isOvo($paymentMethod)
    {
        return in_array($paymentMethod, $this->getOvoMethods());
    }

    public function getSprintPaymentMethods()
    {
        return array_merge(
            $this->getVirtualAccountMethods(),
            $this->getDebitMethods(),
            $this->getCreditMethods(),
            $this->getInstallmentMethods(),
            $this->getOvoMethods()
        );
    }

    public function getVirtualAccountMethods()
    {
        return self::VIRTUAL_ACCOUNT;
    }

    public function getCreditMethods()
    {
        return self::CREDIT;
    }

    public function getDebitMethods()
    {
        return self::DEBIT;
    }

    public function getOvoMethods()
    {
        return self::OVO;
    }

    public function getInstallmentMethods()
    {
        return self::INSTALLMENT;
    }

    public function isPaymentNeeded($paymentMethod)
    {
        return !in_array($paymentMethod, $this->getVirtualAccountMethods());
    }

    public function getWebsiteBanking($paymentMethod)
    {
        return $this->scopeConfig->getValue('sm_payment/virtual_account/'.$paymentMethod.'/website')??'';
    }

    public function getMinimumAmountVA()
    {
        return $this->scopeConfig->getValue('sm_payment/virtual_account/minimum_amount');
    }

    public function getMinimumAmountCC()
    {
        return $this->scopeConfig->getValue('sm_payment/credit_card/minimum_amount');
    }

    public function getTimeDisplayConfirmPage()
    {
        return $this->scopeConfig->getValue('sm_payment/general/time_display_confirm_page');
    }
    public function getInstalmentTerm($paymentMethod, $quote = null)
    {
        $instalmentConfig = $this->configHelper->getInstallmentTerm($paymentMethod);
        if (empty($instalmentConfig)) {
            return '';
        }
        $terms = $this->json->unserialize($instalmentConfig);

        if (!$quote) {
            $quote = $this->sessionCheckout->getQuote();
        }

        $grandTotal = $quote->getSubtotalWithDiscount();
        /** @var Address $address */
        foreach ($quote->getAllShippingAddresses() as $address) {
            $grandTotal += $address->getShippingInclTax();
        }

        if (!empty($terms) && is_array($terms)) {
            $arrayTerm = [];
            foreach ($terms as $key => $value) {
                if ($value['enable']) {
                    $serviceFeeValue = 0;
                    if (isset($value['serviceFee'])) {
                        $serviceFeeValue = $value['serviceFee'];
                    }

                    $serviceFee = ($grandTotal*(int) $serviceFeeValue)/100;
                    $term  = [
                        'label' => __('%1% Installment - %2x', $serviceFeeValue, $value['term']),
                        'value' => $value['term'],
                        'grandTotal' => $grandTotal,
                        'serviceFee' => __('+ Service fee %1', $this->pricingData->currency($serviceFee, true, false)),
                        'serviceFeeAmount' => $serviceFee,
                        'serviceFeeValue' => $serviceFeeValue,
                        'totalFee'=>$this->pricingData->currency($grandTotal + $serviceFee, true, false),
                        'totalFeePerMonth'=>$this->pricingData->currency(round($grandTotal/ (int)$value['term']), true, false)
                    ];

                    array_push($arrayTerm, $term);
                }
            }
            return $arrayTerm;
        }
        return '';
    }

    /**
     * @param string $paymentMethod
     * @param int    $term
     * @param null   $quote
     * @return array|mixed|string
     */
    public function getTermInfo($paymentMethod, $term, $quote = null)
    {
        $terms=$this->getInstalmentTerm($paymentMethod, $quote);
        foreach ($terms as $value) {
            if ($value['value']==$term) {
                return $value;
            }
        }
        return [];
    }
}
