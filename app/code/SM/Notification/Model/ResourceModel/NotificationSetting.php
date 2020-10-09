<?php

namespace SM\Notification\Model\ResourceModel;

class NotificationSetting extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('sm_notification_customer_setting', 'entity_id');
    }
}
