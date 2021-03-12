<?php

namespace SM\Sales\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use SM\Email\Model\Email\Sender as EmailSender;

class OrderInstallation
{
    const EMAIL_TEMPLATE_ORDER_INSTALLATION = 'installation/email/template';
    const EMAIL_TEMPLATE_ORDER_INSTALLATION_SUPPLIER = 'installation/supplier_email/template';
    const EMAIL_SENDER_ORDER_INSTALLATION = 'installation/email/sender';
    const EMAIL_SENDER_ORDER_INSTALLATION_SUPPLIER = 'installation/supplier_email/sender';
    const EMAIL_RECEIVER_ORDER_INSTALLATION = 'installation/email/receiver';
    const XPATH_MAPPING_VARIABLES = 'installation/supplier_email/variables';

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
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * OrderInstallation constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     * @param TimezoneInterface $timezone
     * @param Renderer $addressRenderer
     * @param EmailSender $emailSender
     * @param SenderResolverInterface $senderResolver
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer,
        TimezoneInterface $timezone,
        Renderer $addressRenderer,
        EmailSender $emailSender,
        SenderResolverInterface $senderResolver
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->timezone = $timezone;
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
        $mappingVariables = $this->getMappingVariables();
        $skuList = [];
        foreach ($mappingVariables as $mappingVariable) {
                $skuList[$mappingVariable['sku']] = $mappingVariable['email'];
        }
        foreach ($order->getItems() as $item) {
            $buyRequest = $item->getProductOptionByCode('info_buyRequest');
            if (isset($buyRequest['installation_service'])) {
                if ($buyRequest['installation_service']['is_installation'] == 1) {
                    $isInstallation = true;
                    if (array_key_exists($item->getSku(), $skuList)) {
                        $templateId = $this->getConfigEmailID(self::EMAIL_TEMPLATE_ORDER_INSTALLATION_SUPPLIER);
                        $storeId = (int)$order->getStoreId();
                        $sender = $this->getConfigEmailID(self::EMAIL_SENDER_ORDER_INSTALLATION_SUPPLIER);
                        $email = explode(',', $skuList[$item->getSku()]);
                        $name = $order->getCustomerLastname();
                        $address = $order->getShippingAddress();
                        $templateVars = [
                            "customer_name" => $this->getCustomerName($address),
                            "phone_number" => $address->getData('telephone'),
                            "address" => $this->getCustomerAddress($address),
                            "product" => $item->getName(),
                            "qty" => round($item->getQtyOrdered()),
                            "order_id" => $order->getIncrementId(),
                            "estimate_date_of_delivery" => $this->getEstimateDateOfDelivery($order->getCreatedAt()),
                            "estimate_date_of_receive" => $this->getEstimateDateOfReceive($order->getCreatedAt())
                        ];
                        $this->emailSender->sends(
                            $templateId,
                            $sender,
                            $email,
                            $name,
                            $templateVars,
                            $storeId
                        );
                    }
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

    /**
     * @param string $scopeType
     * @param string|int|null $scopeCode
     * @return array
     */
    public function getMappingVariables($scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        $mappingVariables = $this->scopeConfig->getValue(
            self::XPATH_MAPPING_VARIABLES,
            $scopeType,
            $scopeCode
        );

        return $this->serializer->unserialize($mappingVariables) ?: [];
    }

    public function getCustomerName($address)
    {
        $firstName = trim($address->getFirstName());
        $lastName = trim($address->getLastName());
        return $firstName == $lastName ? $firstName :  $firstName. ' ' . $lastName;
    }

    public function getCustomerAddress($address)
    {
        return $address->getStreet()[0] ? $address->getStreet()[0] : '';
    }

    public function getEstimateDateOfDelivery($createdAt)
    {
        return date('d M Y', strtotime('+ 1 days' ,
            strtotime($this->timezone->date(strtotime($createdAt))->format('d M Y'))
        ));
    }

    public function getEstimateDateOfReceive($createdAt)
    {
        return date('d M Y', strtotime('+ 5 days' ,
            strtotime($this->timezone->date(strtotime($createdAt))->format('d M Y'))
        ));
    }
}
