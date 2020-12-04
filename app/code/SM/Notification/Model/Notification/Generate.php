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
     * Generate constructor.
     *
     * @param \Magento\Framework\App\State               $state
     * @param \SM\Notification\Helper\Config             $configHelper
     * @param \Magento\Store\Model\App\Emulation         $emulation
     * @param \SM\Notification\Model\EventSetting        $eventSetting
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \SM\Notification\Model\NotificationFactory $modelFactory
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        \SM\Notification\Helper\Config $configHelper,
        \Magento\Store\Model\App\Emulation $emulation,
        \SM\Notification\Model\EventSetting $eventSetting,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SM\Notification\Model\NotificationFactory $modelFactory
    ) {
        $this->configHelper = $configHelper;
        $this->emulation = $emulation;
        $this->eventSetting = $eventSetting;
        $this->modelFactory = $modelFactory;
        $this->storeManager = $storeManager;
        $this->stateCode = $this->getState($state);
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
        $this->startEmulator();
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
}
