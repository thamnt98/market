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
        /** @var \SM\Notification\Model\Notification $notify */
        $notify = $this->modelFactory->create();
        $title = 'Someone just signed in to your account on other device.';
        $message = 'Not you? Consider changing your password.';

        $notify->setTitle($title)
            ->setContent($message)
            ->setEvent(\SM\Notification\Model\Notification::EVENT_SERVICE)
            ->setSubEvent(\SM\Notification\Model\Notification::EVENT_UNKNOWN_DEVICE)
            ->setCustomerIds([$customerId])
            ->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_HOME);

        $this->startEmulator();
        $this->eventSetting->init($customerId, \SM\Notification\Model\Notification::EVENT_UNKNOWN_DEVICE);

        if ($this->eventSetting->isPush()) {
            $notify->setPushTitle(__($title)->__toString())
                ->setPushContent(__($message)->__toString());
        }

        if ($this->eventSetting->isEmail()) {
            // Set email
        }

        if ($this->eventSetting->isSms()) {
            // Set sms
        }

        $this->stopEmulator();

        return $notify;
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return \SM\Notification\Model\Notification|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function haveCoupon(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        $store = $this->stateCode === \Magento\Framework\App\Area::AREA_FRONTEND ? null : $customer->getStoreId();
        /** @var \SM\Notification\Model\Notification $notify */
        $notify = $this->modelFactory->create();
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

        $notify->setTitle($title)
            ->setContent($message)
            ->setEvent(\SM\Notification\Model\Notification::EVENT_UPDATE)
            ->setSubEvent(\SM\Notification\Model\Notification::EVENT_PROMO_AND_EVENT)
            ->setCustomerIds([$customer->getId()])
            ->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_VOUCHER_LIST)
            ->setParams($params);

        $this->eventSetting->init($customer->getId(), \SM\Notification\Model\Notification::EVENT_PROMO_AND_EVENT);

        if ($this->eventSetting->isPush()) {
            $notify->setPushTitle(__($title, $params['title'])->__toString())
                ->setPushContent(__($message)->__toString());
        }

        if ($this->eventSetting->isEmail()) {
            // Set email
        }

        if ($this->eventSetting->isSms()) {
            // Set sms
        }

        $this->stopEmulator();

        return $notify;
    }

    /**
     * @param int $helpId
     * @param int $storeId
     *
     * @return \SM\Notification\Model\Notification
     */
    public function termAndPolicy($helpId, $storeId)
    {
        /** @var \SM\Notification\Model\Notification $notify */
        $notify = $this->modelFactory->create();
        $title = 'There is something new for you.';
        $message = 'Check out the new T&C/policy from Transmart';

        $notify->setTitle($title)
            ->setContent($message)
            ->setEvent(\SM\Notification\Model\Notification::EVENT_UPDATE)
            ->setSubEvent(\SM\Notification\Model\Notification::EVENT_INFO)
            ->setRedirectId($helpId)
            ->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_HELP_PAGE)
            ->setData('admin_type', \SM\Notification\Model\Source\CustomerType::TYPE_ALL);

        $this->startEmulator($storeId);
        $notify->setPushTitle(__($title)->__toString());
        $notify->setPushContent(__($message)->__toString());
        $this->stopEmulator();

        return $notify;
    }
}
