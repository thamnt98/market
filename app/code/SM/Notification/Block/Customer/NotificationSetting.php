<?php

namespace SM\Notification\Block\Customer;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class NotificationSetting extends \Magento\Customer\Block\Account\Dashboard
{

    /**
     * @var \SM\Notification\Model\NotificationSettingRepository
     */
    private $notificationSettingRepository;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccountManagement,
        \SM\Notification\Model\NotificationSettingRepository $notificationSettingRepository,
        array $data = []
    ) {
        $this->customerRepository = $customerRepository;
        parent::__construct($context, $customerSession, $subscriberFactory, $customerRepository,
            $customerAccountManagement, $data);
        $this->notificationSettingRepository = $notificationSettingRepository;
    }

    public function getListNotificationSetting()
    {
        $result = [];
        $customerId = $this->customerSession->getCustomerId();
        $listNotificationSetting = $this->notificationSettingRepository->getNotificationSettingArray($customerId, 'web');
        foreach ($listNotificationSetting as $col) {
            if ($col['parent_code'] == '') {
                $result[$col['code']] = [
                    'name' => $col['name'],
                    'type' => [],
                    'child' => []
                ];
            } else {
                if (is_array($result[$col['parent_code']]['type']) && array_search($col['message_type'], $result[$col['parent_code']]['type']) === false) {
                    $result[$col['parent_code']]['type'][] = $col['message_type'];
                }
                $result[$col['parent_code']]['child'][$col['name']][] = [
                    'id' => $col['entity_id'],
                    'type' => $col['message_type'],
                    'value' => $col['default_value']
                ];
            }
        }
        return $result;
    }
}
