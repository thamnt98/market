<?php

namespace SM\Sales\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Sales\Model\Order\Address\Renderer;
use SM\Email\Model\Email\Sender as EmailSender;

class OrderInstallation
{
    const EMAIL_TEMPLATE_ORDER_INSTALLATION = 'installation/email/template';
    const EMAIL_SENDER_ORDER_INSTALLATION = 'installation/email/sender';
    const EMAIL_RECEIVER_ORDER_INSTALLATION = 'installation/email/receiver';

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
     * @var SenderResolverInterface
     */
    protected $senderResolver;

    /**
     * OrderInstallation constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Renderer $addressRenderer
     * @param EmailSender $emailSender
     * @param SenderResolverInterface $senderResolver
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Renderer $addressRenderer,
        EmailSender $emailSender,
        SenderResolverInterface $senderResolver
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->scopeConfig = $scopeConfig;
        $this->emailSender = $emailSender;
        $this->senderResolver = $senderResolver;
    }

    /**
     * @param $order
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendMail($order)
    {
        $isInstallation = false;
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
            $storeId = (int)$order->getStoreId();
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
                $storeId
            );
        }
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
