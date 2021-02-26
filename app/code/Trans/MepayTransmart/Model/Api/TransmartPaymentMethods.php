<?php
/**
 * @category Trans
 * @package  Trans_MepayTransmart
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\MepayTransmart\Model\Api;

use SM\Checkout\Helper\Payment;
use SM\Checkout\Model\Api\PaymentMethods;

class TransmartPaymentMethods extends PaymentMethods
{
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
        parent::__construct(
            $paymentMethodList,
            $methodInstanceFactory,
            $specificationFactory,
            $paymentMethods,
            $installmentMethodFactory,
            $installmentTermFactory,
            $paymentMethodFactory,
            $virtualMethod,
            $creditMethod,
            $bankInterfaceFactory,
            $methodInterfaceFactory,
            $scopeConfig,
            $storeManager,
            $paymentHelper,
            $installmentPayment
        );
    }


    /**
     * @param $quote
     * @param $customerId
     * @return \SM\Checkout\Api\Data\Checkout\PaymentMethodsInterface[]
     */
    public function getMethodsAvailable($quote, $customerId)
    {
        $this->methods = $this->getPaymentMethods($quote);
        //\var_dump($this->methods);
        return [
            $this->setVirtualMethod(),
            $this->setCreditMethod($customerId, $quote),
            $this->setDebitMethod()
        ];
    }

    /**
     * Set debit method
     *
     * @return array
     */
    protected function setDebitMethod()
    {
        $debitMethods = [];
        foreach ($this->methods as $key => $value) {
            if (\strpos($key, 'debit')) {
                $debitMethods[] = $this->bankInterfaceFactory
                ->create()
                ->setCode($key)
                ->setLogo($this->paymentHelper->getLogoPayment($key,true))
                ->setDescription($this->scopeConfig->getValue('payment/sprint/sprint_cc_payment/'.$key.'/description'))
                ->setTitle($this->methods[$key]['title']);
            }
        }

        return $this->paymentMethodFactory
            ->create()
            ->setTitle('Debit Card')
            ->setDescription($this->scopeConfig->getValue('payment/sprint/sprint_cc_payment/description'))
            ->setMethods([
                $this->methodInterfaceFactory
                    ->create()
                    ->setBanks($debitMethods)
                    ->setMinimumAmount($this->paymentHelper->getMinimumAmountCC())
                    ->setTitle('Full payment')
                    ->setDescription(__('Pay your order in full amount'))
                    ->setType('debit_full_payment'),
            ])
            ->setCardType('debit_card')
        ;
    }

    /**
     * Set Virtual
     */
    protected function setVirtualMethod()
    {
        $virtualMethods = [];

        foreach ($this->paymentHelper->getVirtualAccountMethods() as $methodCode) {
            //\var_dump($this->paymentHelper->getVirtualAccountMethods());die();
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
        return $this->paymentMethodFactory->create()
            ->setTitle('Virtual Account')
            ->setMinimumAmount($this->paymentHelper->getMinimumAmountVA())
            ->setDescription($this->scopeConfig->getValue('payment/sprint/sprint_channel/description'))
            ->setMethods([
                $this->methodInterfaceFactory->create()
                ->setBanks($virtualMethods)
                ->setTitle('Virtual Account Full Payment')
                ->setDescription(__('Pay your order in full amount'))
                ->setType('virtual_account_number')
            ])
            ->setCardType('virtual_account');
    }

    /**
     * Set Credit Card
     * @param $customerId
     * @param $quote
     */
    protected function setCreditMethod($customerId, $quote)
    {
        $creditMethods = $installmentMethods = [];

        //set original credit card method
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

        //set additional credit card method
        foreach ($this->getAdditionalCreditCardMethod() as $key => $value) {
            if (isset($this->methods[$value])) {
                $creditMethods[] = $this->bankInterfaceFactory
                    ->create()
                    ->setCode($value)
                    ->setLogo($this->paymentHelper->getLogoPayment($value,true))
                    ->setDescription($this->scopeConfig->getValue('payment/sprint/sprint_cc_payment/'.$methodCode.'/description'))
                    ->setTitle($this->methods[$value]['title']);
            }
        }

        //set installment credit card method
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
                            ->setTotalFeePerMonth($term['totalFeePerMonth'])
                        ;
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
        $methodList = [];
        if (!empty($creditMethods)) {
            $methodList[] = $this->methodInterfaceFactory->create()
                ->setBanks($creditMethods)
                ->setMinimumAmount($this->paymentHelper->getMinimumAmountCC())
                ->setTitle('Full payment')
                ->setDescription(__('Pay your order in full amount'))
                ->setType('cc_full_payment');
        }
        if (!empty($installmentMethods)) {
            $methodList[] = $this->methodInterfaceFactory->create()
                ->setBanks($installmentMethods)
                ->setMinimumAmount($this->paymentHelper->getMinimumAmountCC())
                ->setTitle('Installment')
                ->setDescription(__('Select preferred installment type & tenure'))
                ->setType('cc_installment');
        }
        return $this->paymentMethodFactory->create()
            ->setTitle('Credit Card')
            ->setDescription($this->scopeConfig->getValue('payment/sprint/sprint_cc_payment/description'))
            ->setMethods($methodList)->setCardType('credit_card');
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
     * Get additional credit card
     * @return array
     */
    public function getAdditionalCreditCardMethod()
    {
        return [\Trans\Mepay\Model\Config\Provider\CcDebit::CODE];
    }
}
