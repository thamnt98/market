<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: December, 02 2020
 * Time: 5:38 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model\Notification;

/**
 * Class Generate
 *
 * @package SM\Notification\Model\Notification
 * Generate step
 *   initNotification -> prepare[Event] -> <setNotificationTranslation> -> <setNotificationEmail>
 */
class Generate
{
    /**
     * @var \SM\Notification\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $emulation;

    /**
     * @var \SM\Notification\Model\EventSetting
     */
    protected $eventSetting;

    /**
     * @var \SM\Notification\Model\NotificationFactory
     */
    protected $modelFactory;

    /**
     * @var \SM\Notification\Model\Notification
     */
    protected $notify;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string
     */
    protected $stateCode;

    /**
     * @var \SM\MyVoucher\Model\RuleRepository
     */
    protected $ruleRepository;

    /**
     * @var \SM\Notification\Helper\Generate\Email
     */
    protected $emailHelper;

    /**
     * Generate constructor.
     *
     * @param \Magento\Framework\App\State               $state
     * @param \SM\Notification\Helper\Config             $configHelper
     * @param \Magento\Store\Model\App\Emulation         $emulation
     * @param \SM\Notification\Model\EventSetting        $eventSetting
     * @param \SM\MyVoucher\Model\RuleRepository         $ruleRepository
     * @param \SM\Notification\Helper\Generate\Email     $emailHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \SM\Notification\Model\NotificationFactory $modelFactory
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        \SM\Notification\Helper\Config $configHelper,
        \Magento\Store\Model\App\Emulation $emulation,
        \SM\Notification\Model\EventSetting $eventSetting,
        \SM\MyVoucher\Model\RuleRepository $ruleRepository,
        \SM\Notification\Helper\Generate\Email $emailHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SM\Notification\Model\NotificationFactory $modelFactory
    ) {
        $this->configHelper = $configHelper;
        $this->emulation = $emulation;
        $this->eventSetting = $eventSetting;
        $this->modelFactory = $modelFactory;
        $this->storeManager = $storeManager;
        $this->stateCode = $this->getState($state);
        $this->ruleRepository = $ruleRepository;
        $this->emailHelper = $emailHelper;
    }

    /**
     * @return \SM\Notification\Model\Notification
     */
    public function getNotify()
    {
        return $this->notify;
    }

    /**
     * @param int|null $store
     */
    protected function startEmulator($store = null)
    {
        if ($this->stateCode !== \Magento\Framework\App\Area::AREA_FRONTEND) {
            if (!$store) {
                try {
                    $store = $this->storeManager->getStore()->getId();
                } catch (\Exception $exception) {
                    $store = $this->storeManager->getDefaultStoreView()->getId();
                }
            }

            $this->emulation->startEnvironmentEmulation(
                $store,
                \Magento\Framework\App\Area::AREA_FRONTEND,
                strpos($this->stateCode, 'webapi') !== false
            );
        }
    }

    protected function stopEmulator()
    {
        if ($this->stateCode !== \Magento\Framework\App\Area::AREA_FRONTEND) {
            $this->emulation->stopEnvironmentEmulation();
        }
    }

    protected function getState(\Magento\Framework\App\State $state)
    {
        try {
            return $state->getAreaCode();
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * @param int $customerId
     *
     * @return \SM\Notification\Model\Notification
     */
    public function loginOtherDevice($customerId)
    {
        $title = 'Someone just signed in to your account on other device.';
        $message = 'Not you? Consider changing your password.';

        $this->initNotification([$customerId], $title, $message)
            ->prepareUnknownDevice()
            ->setNotificationTranslation();

        return $this->getNotify();
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return \SM\Notification\Model\Notification|void
     * @throws \Exception
     */
    public function haveCoupon(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        $store = $this->stateCode === \Magento\Framework\App\Area::AREA_FRONTEND ? null : $customer->getStoreId();
        $customerName = $customer->getFirstname() . ' ' .
            ($customer->getMiddlename() ? $customer->getMiddlename() . ' ' : '') .
            $customer->getLastname();

        $this->startEmulator($store);
        $voucherCount = count($this->ruleRepository->getVoucherByCustomer($customer->getId()));
        if ($voucherCount && $voucherCount > 1) {
            $title = "%1, you've got new vouchers.";
            $message = 'Check them out and shop now!';
        } elseif ($voucherCount && $voucherCount == 1) {
            $title = '%1, we have a voucher for you.';
            $message = 'Check them out and shop now!';
        } else {
            return;
        }

        $params = [
            'title' => [
                $customerName,
            ],
        ];

        $this
            ->initNotification([$customer->getId()], $title, $message, $params)
            ->preparePromo()
            ->setNotificationTranslation(null, $store);

        return $this->getNotify();
    }

    /**
     * @param int $helpId
     * @param int $storeId
     *
     * @return \SM\Notification\Model\Notification
     */
    public function termAndPolicy($helpId, $storeId)
    {
        $title = 'There is something new for you.';
        $message = 'Check out the new T&C/policy from Transmart';

        $this
            ->initNotification([], $title, $message)
            ->prepareInformation(\SM\Notification\Model\Source\RedirectType::TYPE_HELP_PAGE, $helpId)
            ->setNotificationTranslation(null, $storeId);

        return $this->getNotify();
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param bool                       $isAll
     *
     * @return \SM\Notification\Model\Notification
     */
    public function refundOrder($order, $isAll = false)
    {
        $orderPayment = $order->getPayment()->getMethod();
        $vaPayments   = $this->configHelper->getVaPaymentList($order->getStoreId());
        $cardPayments = $this->configHelper->getCardPaymentList($order->getStoreId());

        $title   = '%1, we are sorry...';
        $params  = [
            'title' => [
                $order->getCustomerName(),
            ],
        ];

        if (in_array($orderPayment, $vaPayments)) {
            if ($isAll) {
                $message = 'We found unavailable item(s) in your order. Tap here to see the list & request a refund.';
            } else {
                $message = 'Some products in your order are not available. '
                    . 'Tap to see the details & get a refund with a few easy steps.';
            }
        } elseif (in_array($orderPayment, $cardPayments)) {
            if ($isAll) {
                $message = 'We found unavailable item(s) in your order. '
                    . 'Your card will not be charged for this transaction.';
            } else {
                $message = 'Some products in your order are not available. '
                    . 'Your card will not be charged for these products.';
            }
        } else {
            return null;
        }

        $this->initNotification([$order->getCustomerId()], $title, $message, $params);
        $this
            ->prepareOrder($order->getData('parent_order'))
            ->setNotificationTranslation(null, $order->getStoreId());

        return $this->getNotify();
    }

    /**
     * @param int[]  $customerIds
     * @param string $title
     * @param string $message
     * @param array  $params
     *
     * @return Generate
     */
    public function initNotification($customerIds, $title, $message, $params = [])
    {
        $this->notify = $this->modelFactory->create()
            ->setTitle($title)
            ->setContent($message);

        if (count($params)) {
            $this->notify->setParams($params);
        }

        if (empty($customerIds)) {
            $this->notify->setAdminType(\SM\Notification\Model\Source\CustomerType::TYPE_ALL);
        } else {
            $this->notify->setCustomerIds($customerIds);
        }

        return $this;
    }

    /**
     * @param int $orderId
     *
     * @return Generate
     */
    public function prepareOrder($orderId)
    {
        if ($this->notify) {
            if (count($this->notify->getCustomerIds()) == 1) {
                $this->eventSetting->init(
                    $this->notify->getCustomerIds()[0],
                    \SM\Notification\Model\Notification::EVENT_ORDER_STATUS
                );
            }

            $this->notify->setEvent(\SM\Notification\Model\Notification::EVENT_ORDER_STATUS)
                ->setSubEvent(\SM\Notification\Model\Notification::EVENT_ORDER_STATUS)
                ->setRedirectId($orderId)
                ->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_ORDER_DETAIL);
        }

        return $this;
    }

    /**
     * @return Generate
     */
    public function prepareUnknownDevice()
    {
        if ($this->notify) {
            if (count($this->notify->getCustomerIds()) == 1) {
                $this->eventSetting->init(
                    $this->notify->getCustomerIds()[0],
                    \SM\Notification\Model\Notification::EVENT_UNKNOWN_DEVICE
                );
            }

            $this->notify
                ->setEvent(\SM\Notification\Model\Notification::EVENT_SERVICE)
                ->setSubEvent(\SM\Notification\Model\Notification::EVENT_UNKNOWN_DEVICE)
                ->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_HOME);
        }

        return $this;
    }

    /**
     * @param $redirectType
     * @param $redirectId
     *
     * @return Generate
     */
    public function prepareInformation($redirectType, $redirectId)
    {
        if ($this->notify) {
            if (count($this->notify->getCustomerIds()) == 1) {
                $this->eventSetting->init(
                    $this->notify->getCustomerIds()[0],
                    \SM\Notification\Model\Notification::EVENT_INFO
                );
            }

            $this->notify
                ->setEvent(\SM\Notification\Model\Notification::EVENT_UPDATE)
                ->setSubEvent(\SM\Notification\Model\Notification::EVENT_INFO);
            if ($redirectType) {
                $this->notify->setRedirectType($redirectType);
                if ($redirectId) {
                    $this->notify->setRedirectId($redirectId);
                }
            }
        }

        return $this;
    }

    /**
     * @param int|null $ruleId
     *
     * @return Generate
     */
    public function preparePromo($ruleId = null)
    {
        if ($this->notify) {
            if (count($this->notify->getCustomerIds()) == 1) {
                $this->eventSetting->init(
                    $this->notify->getCustomerIds()[0],
                    \SM\Notification\Model\Notification::EVENT_UNKNOWN_DEVICE
                );
            }

            $this->notify
                ->setEvent(\SM\Notification\Model\Notification::EVENT_UPDATE)
                ->setSubEvent(\SM\Notification\Model\Notification::EVENT_PROMO_AND_EVENT);

            if (!$ruleId) {
                $this->notify->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_VOUCHER_LIST);
            } else {
                $this->notify
                    ->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_VOUCHER_DETAIL)
                    ->setRedirectId($ruleId);
            }
        }

        return $this;
    }

    /**
     * @param int|string $templateId
     * @param array      $params
     *
     * @return Generate
     */
    public function setNotificationEmail($templateId, $params = [])
    {
        if ($this->notify && (!$this->eventSetting->isInit() || $this->eventSetting->isEmail())) {
            $this->notify
                ->setEmailTemplate($templateId)
                ->setEmailParams($params);
        }

        return $this;
    }

    /**
     * @param string   $sms
     * @param int|null $storeId
     *
     * @return Generate
     */
    public function setNotificationTranslation($sms = null, $storeId = null)
    {
        if ($this->notify) {
            $this->startEmulator($storeId);
            if ($sms && (!$this->eventSetting->isInit() || $this->eventSetting->isSms())) {
                $this->notify->setSms(__($sms)->__toString());
            }

            if (!$this->eventSetting->isInit() || $this->eventSetting->isPush()) {
                $this->notify->setPushTitle(
                    __($this->notify->getTitle(), $this->notify->getParams()['title'] ?? [])->__toString()
                )->setPushContent(
                    __($this->notify->getContent(), $this->notify->getParams()['content'] ?? [])->__toString()
                );
            }
            $this->stopEmulator();
        }

        return $this;
    }
}
