<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: November, 10 2020
 * Time: 2:13 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model\Source\Config;

class PaymentMethod implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $appConfig;

    /**
     * @var \Magento\Payment\Model\Config
     */
    protected $paymentConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $appConfigScopeConfigInterface
     * @param \Magento\Payment\Model\Config                      $paymentModelConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $appConfigScopeConfigInterface,
        \Magento\Payment\Model\Config $paymentModelConfig
    ) {

        $this->appConfig = $appConfigScopeConfigInterface;
        $this->paymentConfig = $paymentModelConfig;
    }

    public function toOptionArray()
    {
        $payments = $this->paymentConfig->getActiveMethods();
        $methods = [];
        foreach ($payments as $paymentCode => $paymentModel) {
            $paymentTitle = $this->appConfig->getValue('payment/' . $paymentCode . '/title');
            $methods[$paymentCode] = [
                'label' => $paymentTitle,
                'value' => $paymentCode,
            ];
        }

        return $methods;
    }
}
