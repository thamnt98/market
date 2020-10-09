<?php

namespace SM\Notification\Model\ResourceModel\NotificationSetting;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \SM\Notification\Model\NotificationSetting::class,
            \SM\Notification\Model\ResourceModel\NotificationSetting::class
        );
    }
}
