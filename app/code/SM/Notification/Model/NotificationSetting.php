<?php

namespace SM\Notification\Model;

class NotificationSetting extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'sm_notification_setting';

    protected function _construct()
    {
        $this->_init(\SM\Notification\Model\ResourceModel\NotificationSetting::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
