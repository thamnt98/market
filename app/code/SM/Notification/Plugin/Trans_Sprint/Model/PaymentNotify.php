<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: June, 16 2020
 * Time: 4:42 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Plugin\Trans_Sprint\Model;

use SM\DigitalProduct\Model\Cart\Data\Digital as DigitalProduct;
use SM\Notification\Helper\Data as Helper;
use Trans\Sprint\Helper\Config;
use SM\DigitalProduct\Api\Data\Order\DigitalProductInterface as Digital;
use SM\DigitalProduct\Helper\Category\Data as DigitalData;
use SM\Notification\Model\Notification\Consumer\Email as EmailConsumer;

class PaymentNotify
{
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \SM\Notification\Model\NotificationFactory
     */
    protected $notificationFactory;

    /**
     * @var \SM\Notification\Model\ResourceModel\Notification
     */
    protected $notificationResource;

    /**
     * @var \SM\Notification\Model\EventSetting
     */
    protected $setting;

    /**
     * @var string
     */
    protected $paymentStatusCode;

    /**
     * @var \SM\Notification\Helper\Generate\Email
     */
    protected $emailHelper;

    /**
     * @var \Magento\Framework\Logger\Monolog|null
     */
    protected $logger;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $emulation;

    /**
     * PaymentNotify constructor.
     *
     * @param Helper                                                     $helper
     * @param \Magento\Store\Model\App\Emulation                         $emulation
     * @param \SM\Notification\Helper\Generate\Email                     $emailHelper
     * @param \SM\Notification\Model\EventSetting                        $setting
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \SM\Notification\Model\NotificationFactory                 $notificationFactory
     * @param \SM\Notification\Model\ResourceModel\Notification          $notificationResource
     * @param \Magento\Framework\Logger\Monolog|null                     $logger
     */
    public function __construct(
        Helper $helper,
        \Magento\Store\Model\App\Emulation $emulation,
        \SM\Notification\Helper\Generate\Email $emailHelper,
        \SM\Notification\Model\EventSetting $setting,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \SM\Notification\Model\NotificationFactory $notificationFactory,
        \SM\Notification\Model\ResourceModel\Notification $notificationResource,
        \Magento\Framework\Logger\Monolog $logger = null
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->notificationFactory = $notificationFactory;
        $this->notificationResource = $notificationResource;
        $this->setting = $setting;
        $this->emailHelper = $emailHelper;
        $this->logger = $logger;
        $this->paymentStatusCode = null;
        $this->helper = $helper;
        $this->emulation = $emulation;
    }

    public function afterProcessingNotify(
        \Trans\Sprint\Api\PaymentNotifyInterface $subject,
        $result,
        $postData
    ) {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $coll */
        $coll = $this->orderCollectionFactory->create();
        $coll->addFieldToFilter('reference_number', $postData['transactionNo']);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $coll->getFirstItem();
        if (empty($postData['transactionStatus']) || !$order->getId() || !$order->getPayment()) {
            $this->logError("Can't generate notification", $postData);

            return $result;
        } else {
            $this->paymentStatusCode = $postData['transactionStatus'];
        }

        try {
            $this->setting->init($order->getCustomerId(), \SM\Notification\Model\Notification::EVENT_ORDER_STATUS);
            $this->createNotification($order);
            $this->logInfo('Generate `payment notification` Success', $postData);
        } catch (\Exception $e) {
            $this->logError(
                "Generate `payment notification` error:\n\t" . $e->getMessage(),
                [
                    'data'  => ['order_id' => $order->getId(), 'post_data' => $postData],
                    'trace' => $e->getTrace(),
                ]
            );
        }

        return $result;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return int
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    protected function createNotification($order)
    {
        switch ($this->paymentStatusCode) {
//            case Config::PAYMENT_FLAG_DECLINED_04: // Payment Expired
//                return $this->createExpired($order); // Payment not call when expired --> move to cron.
            case Config::PAYMENT_FLAG_DECLINED_05: // Payment Reject by Bank
                return $this->createBankReject($order);
            case Config::PAYMENT_FLAG_DECLINED_01: // Payment System Failed
            case Config::PAYMENT_FLAG_DECLINED_02:
            case Config::PAYMENT_FLAG_DECLINED_03:
            case Config::PAYMENT_FLAG_DECLINED_06:
                return $this->createSystemFailed($order);
            case Config::PAYMENT_FLAG_SUCCESS_CODE: // Payment Success
            case Config::TRANSACTION_STATUS_SUCCESS:
                return $this->createSuccess($order);
            default:
                return 0;
        }
    }

    /**
     * @param string $message
     * @param array  $params
     */
    protected function logError($message, $params)
    {
        if ($this->logger) {
            $this->logger->error($message, $params);
        }
    }

    /**
     * @param string $message
     * @param array  $params
     */
    protected function logInfo($message, $params)
    {
        if ($this->logger) {
            $this->logger->info($message, $params);
        }
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return int
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    protected function createExpired($order)
    {
        $title = 'Sorry, your order has been cancelled.';
        $content = 'Order %1 has passed the payment due time.';
        $params = [
            'content' => [
                $order->getData('reference_order_id')
                ?? $order->getData('reference_number')
                ?? $order->getIncrementId(),
            ],
        ];
        /** @var \SM\Notification\Model\Notification $notification */
        $notification = $this->notificationFactory->create();
        $notification->setTitle($title)
            ->setContent($content)
            ->setImage($this->helper->getMediaPathImage(Helper::XML_IMAGE_PAYMENT_FAILED, $order->getStoreId()))
            ->setParams($params);
        $this->addNotificationAdditional(
            $notification,
            $order,
            $this->emailHelper->getExpiredTemplateId($order->getStoreId())
        );

        $this->notificationResource->save($notification);

        return $notification->getId();
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return int
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    protected function createBankReject($order)
    {
        $title = 'Sorry, your order is cancelled because the payment is rejected by your bank.';
        $content = "Don't worry, you are not charged for order %1";
        $params = [
            'content' => [
                $order->getData('reference_order_id')
                ?? $order->getData('reference_number')
                ?? $order->getIncrementId(),
            ],
        ];

        $notification = $this->initNotification($order);
        $notification->setTitle($title)
            ->setContent($content)
            ->setImage($this->helper->getMediaPathImage(Helper::XML_IMAGE_PAYMENT_FAILED, $order->getStoreId()))
            ->setParams($params);
        $this->addNotificationAdditional(
            $notification,
            $order,
            $this->emailHelper->getBankRejectTemplateId($order->getStoreId())
        );

        $this->notificationResource->save($notification);

        return $notification->getId();
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return int
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    protected function createSystemFailed($order)
    {
        if ($order->getIsVirtual()) {
            /** @var \Magento\Sales\Model\Order\Item $item */
            $item = $order->getItemsCollection()->getFirstItem();
            $buyRequest = $item->getProductOptionByCode('info_buyRequest') ?? [];
            if (empty($buyRequest[Digital::DIGITAL])) {
                return 0;
            }

            $title = "We're sorry. Your %1 payment is unsuccessful.";
            $content = "Don't worry, you're not charged yet. Please redo your transaction in Top Up & Bills.";
            $params = [
                'title' => [
                    $buyRequest[Digital::DIGITAL][DigitalProduct::SERVICE_TYPE] ?? '',
                ],
            ];
            $email = $this->emailHelper->getPaymentSystemFailedDigitalTemplateId($order->getStoreId());
        } else {
            $title = 'Sorry, your order is cancelled due to payment system failure.';
            $content = "Don't worry, you are not charged for order %1";
            $params = [
                'content' => [
                    $order->getData('reference_order_id')
                    ?? $order->getData('reference_number')
                    ?? $order->getIncrementId(),
                ],
            ];
            $email = $this->emailHelper->getSystemFailedTemplateId($order->getStoreId());
        }

        $notification = $this->initNotification($order);
        $notification->setTitle($title)
            ->setContent($content)
            ->setImage($this->helper->getMediaPathImage(Helper::XML_IMAGE_PAYMENT_FAILED, $order->getStoreId()))
            ->setParams($params);
        $this->addNotificationAdditional($notification, $order, $email);

        $this->notificationResource->save($notification);

        return $notification->getId();
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return int
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    protected function createSuccess($order)
    {
        $content = $email = $title = '';
        $params = [];

        if (!$order->getIsVirtual()) {
            $email = $this->emailHelper->getPaymentSuccessPhysicalTemplateId($order->getStoreId());
            $title = 'Your payment is successful!';
            $content = 'Order %1 is now being processed. Check out the progress in My Order.';
            $params = [
                'content' => [
                    $order->getData('reference_order_id')
                    ?? $order->getData('reference_number')
                    ?? $order->getIncrementId(),
                ],
            ];
        } else {
            /** @var \Magento\Sales\Model\Order\Item $item */
            $item = $order->getItemsCollection()->getFirstItem();
            $buyRequest = $item->getProductOptionByCode('info_buyRequest') ?? [];
            if (empty($buyRequest[Digital::SERVICE_TYPE])) {
                return 0;
            }

            switch ($buyRequest[Digital::SERVICE_TYPE]) {
                case DigitalData::TOP_UP_VALUE:
                    $email = $this->emailHelper->getPaymentSuccessDigitalTopUpTemplateId($order->getStoreId());
                    $title = 'Your %1 %2 top up is successful!';
                    $params = [
                        'title' => [
                            $buyRequest[Digital::DIGITAL][DigitalProduct::PRICE] ?? 'Rp 0',
                            $buyRequest[Digital::DIGITAL][DigitalProduct::OPERATOR] ?? '',
                        ],
                    ];
                    break;
                case DigitalData::MOBILE_PACKAGE_INTERNET_VALUE:
                    $email = $this->emailHelper->getPaymentSuccessDigitalMobilePackageTemplateId($order->getStoreId());
                    $title = 'Your %1 package purchase is successful!';
                    $params = [
                        'title' => [
                            $buyRequest[Digital::DIGITAL][DigitalProduct::PRODUCT_NAME] ?? '',
                        ],
                    ];
                    break;
                case DigitalData::MOBILE_PACKAGE_ROAMING_VALUE:
                    $email = $this->emailHelper->getPaymentSuccessDigitalMobileRoamingTemplateId($order->getStoreId());
                    $title = 'You have successfully purchased Mobile Roaming Package';
                    break;
                case DigitalData::ELECTRICITY_TOKEN_VALUE:
                    $email = $this->emailHelper->getPaymentSuccessDigitalPlnTokenTemplateId($order->getStoreId());
                    $title = 'Your %1 PLN token purchase is successful!';
                    $params = [
                        'title' => [
                            $buyRequest[Digital::DIGITAL][DigitalProduct::PRICE] ?? 'O Rp',
                        ],
                    ];
                    break;
                case DigitalData::ELECTRICITY_BILL_VALUE:
                    $title = 'Your %1 PLN bill has been successfully paid!';
                    $params = [
                        'title' => [
                            $buyRequest[Digital::DIGITAL][DigitalProduct::PERIOD] ?? '',
                        ],
                    ];
                    break;
                case DigitalData::MOBILE_POSTPAID_VALUE:
                    $email = $this->emailHelper->getPaymentSuccessDigitalMobilePostpaidTemplateId($order->getStoreId());
                    $title = 'Your %1 mobile postpaid bill has been successfully paid!';
                    $params = [
                        'title' => [
                            $buyRequest[Digital::DIGITAL][DigitalProduct::PERIOD] ?? '',
                        ],
                    ];
                    break;
                default:
                    return 0;
            }
        }

        $notification = $this->initNotification($order);
        $notification->setTitle($title)
            ->setContent($content)
            ->setImage($this->helper->getMediaPathImage(Helper::XML_IMAGE_PAYMENT_SUCCESS, $order->getStoreId()))
            ->setParams($params);
        $this->addNotificationAdditional($notification, $order, $email);

        $this->notificationResource->save($notification);

        return $notification->getId();
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return \SM\Notification\Model\Notification
     */
    protected function initNotification($order)
    {
        /** @var \SM\Notification\Model\Notification $notification */
        $notification = $this->notificationFactory->create();
        $notification->setEvent(\SM\Notification\Model\Notification::EVENT_ORDER_STATUS)
            ->setSubEvent(\SM\Notification\Model\Notification::EVENT_ORDER_STATUS)
            ->setCustomerIds([$order->getCustomerId()])
            ->setRedirectId($order->getData('parent_order') ? $order->getData('parent_order') : $order->getId())
            ->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_ORDER_DETAIL);

        return $notification;
    }

    /**
     * @param \SM\Notification\Model\Notification $notify
     * @param \Magento\Sales\Model\Order          $order
     * @param int|string|null                     $email
     * @param string|null                         $sms
     */
    protected function addNotificationAdditional($notify, $order, $email = null, $sms = null)
    {
        $this->emulation->startEnvironmentEmulation(
            $order->getStoreId(),
            \Magento\Framework\App\Area::AREA_FRONTEND,
            true
        );

        $params = $notify->getParams() ?: [];
        if ($this->setting->isPush()) {
            $notify->setPushTitle(__($notify->getTitle(), $params['title'] ?? [])->__toString())
                ->setPushContent(__($notify->getContent(), $params['content'] ?? [])->__toString());
        }

        if ($this->setting->isEmail() && $email) {
            $notify->setEmailTemplate($email)
                ->setEmailParams([
                    EmailConsumer::EMAIL_PARAM_ORDER_KEY => $order->getId(),
                ]);
        }

        if ($this->setting->isSms() && $sms) {
            $notify->setSms(__($sms)->__toString());
        }

        $this->emulation->stopEnvironmentEmulation();
    }
}
