<?php

namespace SM\Sales\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address\Renderer;
use SM\Email\Model\Email\Sender as EmailSender;

class OrderInstallation
{
    const EMAIL_TEMPLATE_ORDER_INSTALLATION   = 'installation/email/template';
    const EMAIL_SENDER_ORDER_INSTALLATION     = 'installation/email/sender';
    const EMAIL_RECEIVER_ORDER_INSTALLATION   = 'installation/email/receiver';

    /**
     * @var EmailSender
     */
    protected $emailSender;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Renderer
     */
    protected $addressRenderer;

    /**
     * PaymentNotify constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param Renderer $addressRenderer
     * @param EmailSender $emailSender
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Renderer $addressRenderer,
        EmailSender $emailSender
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->scopeConfig = $scopeConfig;
        $this->emailSender = $emailSender;
    }

    public function afterGetOrder(
        \SM\Help\Block\ContactUs\OrderProduct $subject,
        $result
    ) {
        $isInstallation = false;
        $order = $result;
        if ($order) {
            foreach ($order->getItems() as $item) {
                $buyRequest = $item->getProductOptionByCode('info_buyRequest');
                if (isset($buyRequest['installation_service'])) {
                    if ($buyRequest['installation_service']['is_installation'] == 1) {
                        $isInstallation = true;
                        break;
                    }
                }
            }
            if ($isInstallation == true) {
                $templateId = $this->getConfigEmailID(self::EMAIL_TEMPLATE_ORDER_INSTALLATION);
                $sender = $this->getConfigEmailID(self::EMAIL_SENDER_ORDER_INSTALLATION);
                $email = $this->getConfigEmailID(self::EMAIL_RECEIVER_ORDER_INSTALLATION);
                $name = $order->getCustomerLastname();
                $templateVars = [
                    "order" => $order,
                    "formattedShippingAddress" => $this->getFormattedShippingAddress($order)
                ];
                $this->emailSender->send(
                    $templateId,
                    $sender,
                    $email,
                    $name,
                    $templateVars,
                    (int) $order->getStoreId()
                );
            }
        }
        return $result;
    }

    /**
     * @param      $type
     * @param null $store
     *
     * @return mixed
     */
    public function getConfigEmailID($type, $store = null)
    {
        return $this->scopeConfig->getValue(
            $type,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Render shipping address into html.
     *
     * @param $order
     * @return string|null
     */
    protected function getFormattedShippingAddress($order)
    {
        return $order->getIsVirtual()
            ? null
            : $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }
}
