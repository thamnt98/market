<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: June, 13 2020
 * Time: 11:09 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

class CustomerSetting extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \SM\Notification\Model\NotificationSettingRepository
     */
    protected $notificationSettingRepository;

    /**
     * CustomerSetting constructor.
     *
     * @param \SM\Notification\Model\NotificationSettingRepository $notificationSettingRepository
     * @param \Magento\Framework\App\Helper\Context                $context
     */
    public function __construct(
        \SM\Notification\Model\NotificationSettingRepository $notificationSettingRepository,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->notificationSettingRepository = $notificationSettingRepository;
    }

    /**
     * @param $customerId
     *
     * @return array
     */
    public function getCustomerSetting($customerId)
    {
        $result = [];
        $settings = $this->notificationSettingRepository->getNotificationSettingArray($customerId, ['web', 'app']);
        foreach ($settings as $setting) {
            if ((bool)$setting['default_value']) {
                $result[] = $this->generateSettingCode(
                    $setting['event_type'] ?? '',
                    $setting['message_type'] ?? ''
                );
            }
        }

        return array_unique($result);
    }

    /**
     * @param $event
     * @param $message
     *
     * @return string
     */
    public function generateSettingCode($event, $message)
    {
        return $event . '_' . $message;
    }

    /**
     * @param string          $path
     * @param string          $scope
     * @param int|string|null $scopeId
     *
     * @return mixed
     */
    public function getConfigValue($path, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            $scope,
            $scopeId
        );
    }
}
