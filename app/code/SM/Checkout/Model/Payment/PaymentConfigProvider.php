<?php


namespace SM\Checkout\Model\Payment;


use Magento\Checkout\Model\ConfigProviderInterface;

class PaymentConfigProvider  implements ConfigProviderInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig=$scopeConfig;
    }

    public function getConfig()
    {
        $config['payment']['sm_config']['credit_card']['minimum_amount'] = $this->scopeConfig->getValue('sm_payment/credit_card/minimum_amount');
        $config['payment']['sm_config']['virtual_account']['minimum_amount'] = $this->scopeConfig->getValue('sm_payment/virtual_account/minimum_amount');

        return $config;
    }
}
