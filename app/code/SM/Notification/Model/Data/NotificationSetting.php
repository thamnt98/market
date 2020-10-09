<?php

namespace SM\Notification\Model\Data;

class NotificationSetting extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Notification\Api\Data\NotificationSettingInterface
{
    /**
     * {@inheritdoc}
     */
    public function setPushNotification($list)
    {
        return $this->setData('push_notification', $list);
    }
    /**
     * {@inheritdoc}
     */
    public function getPushNotification()
    {
        return $this->_get('push_notification');
    }
    /**
     * {@inheritdoc}
     */
    public function setEmail($list)
    {
        return $this->setData('email', $list);
    }
    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->_get('email');
    }
    /**
     * {@inheritdoc}
     */
    public function setSms($list)
    {
        return $this->setData('sms', $list);
    }
    /**
     * {@inheritdoc}
     */
    public function getSms()
    {
        return $this->_get('sms');
    }
}
