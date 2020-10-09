<?php

namespace SM\Notification\Setup\Patch\Data;

class UpdateNotificationSettingConfig implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $setup;

    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup
    ) {
        $this->setup = $setup;
    }

    public function apply()
    {
        $this->setup->startSetup();
        $tableName = $this->setup->getTable('sm_notification_customer_setting');
        if ($this->setup->getConnection()->isTableExists($tableName) == true) {
            $data = [
                ["notify_me", "order_status_when_sign_out","Order status when I'm signed out", 1, "sms", "order_status_when_sign_out", "all", "sms"],

            ];
            $columns = [
                'parent_code',
                'code',
                'name',
                'default_value',
                'message_type',
                'event_type',
                'area',
                "tab"
            ];
            $this->setup->getConnection()->insertArray($tableName, $columns, $data);


            $this->setup
                ->getConnection()
                ->update(
                    $this->setup->getTable('sm_notification_customer_setting'),
                    ['name' => 'Notify me for'],
                    ['code = "notify_me"']
                );

            $this->setup
                ->getConnection()
                ->update(
                    $this->setup->getTable('sm_notification_customer_setting'),
                    ['name' => 'Unknown device sign in'],
                    ['code = "unknown_device"']
                );


            $this->setup
                ->getConnection()
                ->update(
                    $this->setup->getTable('sm_notification_customer_setting'),
                    ['name' => 'My Order'],
                    ['code = "my_order"']
                );

            $this->setup->getConnection()->delete($this->setup->getTable('sm_notification_customer_setting'),
                "code = 'my_appointment' AND area = 'app' ");
            $this->setup->getConnection()->delete($this->setup->getTable('sm_notification_customer_setting'),
                "code = 'chat_recap' AND area = 'app' ");

        }
        $this->setup->endSetup();
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
