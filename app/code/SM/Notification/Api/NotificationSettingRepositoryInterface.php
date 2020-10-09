<?php

namespace SM\Notification\Api;

interface NotificationSettingRepositoryInterface
{
    /**
     * @param int $customerId
     * @param string $area
     * @return \SM\Notification\Api\Data\NotificationSettingInterface
     */
    public function getNotificationSetting($customerId, $area);

    /**
     * @param int $customerId
     * @param \SM\Notification\Api\Data\NotificationSetting\ChildItemInterface $data
     * @return boolean
     */
    public function updateNotificationSettingData($customerId, $data);
}
