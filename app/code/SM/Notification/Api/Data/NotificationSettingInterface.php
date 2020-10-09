<?php

namespace SM\Notification\Api\Data;

interface NotificationSettingInterface
{
    /**
     * @param \SM\Notification\Api\Data\NotificationSetting\ParentItemInterface[] $list
     * @return $this
     */
    public function setPushNotification($list);

    /**
     * @return \SM\Notification\Api\Data\NotificationSetting\ParentItemInterface[]
     */
    public function getPushNotification();

    /**
     * @param \SM\Notification\Api\Data\NotificationSetting\ParentItemInterface[] $list
     * @return $this
     */
    public function setSms($list);

    /**
     * @return \SM\Notification\Api\Data\NotificationSetting\ParentItemInterface[]
     */
    public function getSms();

    /**
     * @param \SM\Notification\Api\Data\NotificationSetting\ParentItemInterface[] $list
     * @return $this
     */
    public function setEmail($list);

    /**
     * @return \SM\Notification\Api\Data\NotificationSetting\ParentItemInterface[]
     */
    public function getEmail();
}
