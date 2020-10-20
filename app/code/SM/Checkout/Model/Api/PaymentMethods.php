<?php
/**
 * Class PaymentMethods
 * @package SM\Checkout\Model\Api
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Checkout\Model\Api;

use SM\Checkout\Helper\Payment;

class PaymentMethods
{
    const XML_PATH_PAYMENT_METHODS = 'payment';

    /**
     * Different payment method checks.
     */
    const CHECK_USE_FOR_COUNTRY = 'country';

    const CHECK_USE_FOR_CURRENCY = 'currency';

    const CHECK_USE_CHECKOUT = 'checkout';

    const CHECK_USE_INTERNAL = 'internal';

    const CHECK_ORDER_TOTAL_MIN_MAX = 'total';

    const CHECK_ZERO_TOTAL = 'zero_total';

    /**
     * @var \Magento\Payment\Api\PaymentMethodListInterface
     */
    protected $paymentMethodList;

    /**
     * @var \Magento\Payment\Model\Checks\SpecificationFactory
     */
    protected $methodSpecificationFactory;
    /**
     * @var Payment
     */
    protected $paymentHelper;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\PaymentMethodsInterface
     */
    private $paymentMethods;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\PaymentMethods\PaymentMethodInterfaceFactory
     */
    private $paymentMethodFactory;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\PaymentMethods\VirtualMethodInterface
     */
    private $virtualMethod;

    /**
     * @var array
     */
    private $methods;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\PaymentMethods\CreditMethodInterface
     */
    private $creditMethod;

    /**
     * @var \Magento\Payment\Model\Method\InstanceFactory
     */
    private $methodInstanceFactory;

    /**
     * @var \SM\Checkout\Api\PaymentInterface
     */
    private $installmentPayment;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\PaymentMethods\InstallmentMethodInterfaceFactory
     */
    private $installmentMethodFactory;

    /**
     * @var \SM\Checkout\Api\Data\Checkout\PaymentMethods\InstallmentTermInterfaceFactory
     */
    private $installmentTermFactory;
    /**
     * @var \SM\Checkout\Api\Data\Checkout\PaymentMethods\BankInterfaceFactory
     */
    protected $bankInterfaceFactory;
    /**
     * @var \SM\Checkout\Api\Data\Checkout\PaymentMethods\MethodInterfaceFactory
     */
    protected $methodInterfaceFactory;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * PaymentMethods constructor.
     * @param \Magento\Payment\Api\PaymentMethodListInterface                                 $paymentMethodList
     * @param \Magento\Payment\Model\Method\InstanceFactory                                   $methodInstanceFactory
     * @param \Magento\Payment\Model\Checks\SpecificationFactory                              $specificationFactory
     * @param \SM\Checkout\Api\Data\Checkout\PaymentMethodsInterface                          $paymentMethods
     * @param \SM\Checkout\Api\Data\Checkout\PaymentMethods\InstallmentMethodInterfaceFactory $installmentMethodFactory
     * @param \SM\Checkout\Api\Data\Checkout\PaymentMethods\InstallmentTermInterfaceFactory   $installmentTermFactory
     * @param \SM\Checkout\Api\Data\Checkout\PaymentMethods\PaymentMethodInterfaceFactory     $paymentMethodFactory
     * @param \SM\Checkout\Api\Data\Checkout\PaymentMethods\VirtualMethodInterface            $virtualMethod
     * @param \SM\Checkout\Api\Data\Checkout\PaymentMethods\CreditMethodInterface             $creditMethod
     * @param \SM\Checkout\Api\Data\Checkout\PaymentMethods\BankInterfaceFactory              $bankInterfaceFactory
     * @param \SM\Checkout\Api\Data\Checkout\PaymentMethods\MethodInterfaceFactory            $methodInterfaceFactory
     * @param \SM\Checkout\Model\Payment                                                      $installmentPayment
     */
    public function __construct(
        \Magento\Payment\Api\PaymentMethodListInterface $paymentMethodList,
        \Magento\Payment\Model\Method\InstanceFactory $methodInstanceFactory,
        \Magento\Payment\Model\Checks\SpecificationFactory $specificationFactory,
        \SM\Checkout\Api\Data\Checkout\PaymentMethodsInterface $paymentMethods,
        \SM\Checkout\Api\Data\Checkout\PaymentMethods\InstallmentMethodInterfaceFactory $installmentMethodFactory,
        \SM\Checkout\Api\Data\Checkout\PaymentMethods\InstallmentTermInterfaceFactory $installmentTermFactory,
        \SM\Checkout\Api\Data\Checkout\PaymentMethods\PaymentMethodInterfaceFactory $paymentMethodFactory,
        \SM\Checkout\Api\Data\Checkout\PaymentMethods\VirtualMethodInterface $virtualMethod,
        \SM\Checkout\Api\Data\Checkout\PaymentMethods\CreditMethodInterface $creditMethod,
        \SM\Checkout\Api\Data\Checkout\PaymentMethods\BankInterfaceFactory $bankInterfaceFactory,
        \SM\Checkout\Api\Data\Checkout\PaymentMethods\MethodInterfaceFactory $methodInterfaceFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Payment $paymentHelper,
        \SM\Checkout\Model\Payment $installmentPayment
    ) {
        $this->paymentMethodList = $paymentMethodList;
        $this->methodInstanceFactory = $methodInstanceFactory;
        $this->methodSpecificationFactory = $specificationFactory;
        $this->paymentMethods = $paymentMethods;
        $this->paymentMethodFactory = $paymentMethodFactory;
        $this->installmentMethodFactory = $installmentMethodFactory;
        $this->installmentTermFactory = $installmentTermFactory;
        $this->virtualMethod = $virtualMethod;
        $this->creditMethod = $creditMethod;
        $this->installmentPayment = $installmentPayment;
        $this->bankInterfaceFactory = $bankInterfaceFactory;
        $this->methodInterfaceFactory = $methodInterfaceFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * @param $quote
     * @param $customerId
     * @return \SM\Checkout\Api\Data\Checkout\PaymentMethodsInterface[]
     */
    public function getMethodsAvailable($quote, $customerId)
    {
        $this->methods = $this->getPaymentMethods($quote);
        return [
            $this->setVirtualMethod(),
            $this->setCreditMethod($customerId, $quote)
        ];
    }

    /**
     * Set Virtual
     */
    protected function setVirtualMethod()
    {
        $virtualMethods = [];

        foreach ($this->paymentHelper->getVirtualAccountMethods() as $methodCode) {
            if ($this->checkIsSprintMethod($methodCode)) {
                $virtualMethods[] = $this->bankInterfaceFactory
                    ->create()
                    ->setCode($methodCode)
                    ->setLogo($this->paymentHelper->getLogoPayment($methodCode,true))
                    ->setContent($this->paymentHelper->getBlockPaymentNote($methodCode,true))
                    ->setTitle($this->methods[$methodCode]['title'])
                    ->setDescription($this->scopeConfig->getValue('payment/sprint/sprint_channel/'.$methodCode.'/description'))
                    ->setMinimumAmount($this->paymentHelper->getMinimumAmountVA());
            }
        }
        return $this->paymentMethodFactory->create()->setTitle('Virtual Account')
            ->setMinimumAmount($this->paymentHelper->getMinimumAmountVA())
            ->setDescription($this->scopeConfig->getValue('payment/sprint/sprint_channel/description'))
                                   ->setMethods([$this->methodInterfaceFactory->create()->setBanks($virtualMethods)])->setCardType('virtual_account');
    }

    /**
     * Set Credit Card
     * @param $customerId
     * @param $quote
     */
    protected function setCreditMethod($customerId, $quote)
    {
        $creditMethods = $installmentMethods = [];

        foreach ($this->paymentHelper->getCreditMethods() as $methodCode) {
            if ($this->checkIsSprintMethod($methodCode)) {
                $creditMethods[] = $this->bankInterfaceFactory
                    ->create()
                    ->setCode($methodCode)
                    ->setLogo($this->paymentHelper->getLogoPayment($methodCode,true))
                    ->setDescription($this->scopeConfig->getValue('payment/sprint/sprint_cc_payment/'.$methodCode.'/description'))
                    ->setTitle($this->methods[$methodCode]['title']);
            }
        }
        foreach ($this->paymentHelper->getInstallmentMethods() as $methodCode) {
            if ($this->checkIsSprintMethod($methodCode)) {

                $terms = $this->installmentPayment->getInstalmentTerm(
                    $customerId,
                    $methodCode,
                    $quote
                );
                $termData = [];

                foreach ($terms as $term) {
                    $termData[] =
                        $this->installmentTermFactory->create()
                            ->setValue($term['value'])
                            ->setLabel($term['label'])
                            ->setServiceFee($term['serviceFee'])
                            ->setServiceFeeAmount($term['serviceFeeAmount'])
                            ->setServiceFeeValue($term['serviceFeeValue'])
                            ->setTotalFee($term['totalFee'])
                            ->setTotalFeePerMonth($term['totalFeePerMonth']);
                }

                $installmentMethods[] = $this->bankInterfaceFactory
                    ->create()
                    ->setCode($methodCode)
                    ->setTitle($this->methods[$methodCode]['title'])
                    ->setLogo($this->paymentHelper->getLogoPayment($methodCode,true))
                    ->setDescription($this->scopeConfig->getValue('payment/sprint/sprint_cc_payment/'.$methodCode.'/description'))
                    ->setTerms($termData);
            }
        }
        return $this->paymentMethodFactory->create()->setTitle('Credit Card')
            ->setDescription($this->scopeConfig->getValue('payment/sprint/sprint_cc_payment/description'))
                                   ->setMethods([
                                       $this->methodInterfaceFactory->create()->setBanks($creditMethods)
                                           ->setMinimumAmount($this->paymentHelper->getMinimumAmountCC())
                                       ->setTitle('Full payment')->setDescription(__('Pay your order in full amount'))->setType('cc_full_payment'),
                                       $this->methodInterfaceFactory->create()->setBanks($installmentMethods)
                                           ->setMinimumAmount($this->paymentHelper->getMinimumAmountCC())
                                           ->setTitle('Installment')->setDescription(__('Select preferred installment type & tenure'))->setType('cc_installment')
                                   ])->setCardType('credit_card');
    }

    /**
     * @param $methodCode
     * @return bool
     */
    protected function checkIsSprintMethod($methodCode)
    {
        return (array_key_exists($methodCode, $this->methods));
    }

    /**
     * Retrieve all payment methods
     *
     * @return array
     */
    protected function getPaymentMethodxs()
    {
        // return $this->initialConfig->getData('default')[self::XML_PATH_PAYMENT_METHODS];
    }

    /**
     * @param $quote
     * @return array
     */
    protected function getPaymentMethods($quote)
    {
        $store = $quote ? $quote->getStoreId() : null;
        $methods = [];

        foreach ($this->paymentMethodList->getActiveList($store) as $method) {
            $methodInstance = $this->methodInstanceFactory->create($method);
            if ($methodInstance->isAvailable($quote) && $this->canUseMethod($methodInstance, $quote)) {
                $methods[$method->getCode()] = [
                    'title' => $method->getTitle(),
                    'storeId' => $method->getStoreId(),
                    'isActive' => $method->getIsActive()
                ];
            }
        }
        return $methods;
    }

    /**
     * @param $method
     * @param $quote
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function canUseMethod($method, $quote)
    {
        $checks = [
            self::CHECK_USE_FOR_COUNTRY,
            self::CHECK_USE_FOR_CURRENCY,
            self::CHECK_ORDER_TOTAL_MIN_MAX,
            self::CHECK_ZERO_TOTAL
        ];

        return $this->methodSpecificationFactory->create($checks)->isApplicable(
            $method,
            $quote
        );
    }

}
