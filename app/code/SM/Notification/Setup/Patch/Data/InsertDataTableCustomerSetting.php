<?php

namespace SM\Notification\Setup\Patch\Data;

class InsertDataTableCustomerSetting implements \Magento\Framework\Setup\Patch\DataPatchInterface
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
        $tableName = $this->setup->getTable('sm_notification_setting');
        if ($this->setup->getConnection()->isTableExists($tableName) == true) {
            $data = [
                ["", "Transmart Updates", 1, "email", "update", "all", "email"],
                [1, "Promos & Events", 1, "email", "promo", "all", "email"],
                [1, "Information", 1, "email", "information", "all", "email"],
                ["", "My Orders", 1, "email", "order_status", "all", "email"],
                [4, "Order Status", 1, "email", "order_status", "all", "email"],
                [4, "Order Status", 1, "sms", "order_status", "web", ""],
                [4, "Order Status When Signed Out", 0, "email", "order_status", "web", ""],
                [4, "Order Status When Signed Out", 1, "sms", "order_status", "web", ""],
                ["", "Services", 1, "email", "service", "all", "email"],
                [9, "My Appointments", 1, "email", "my_appointment", "all", "email"],
                [9, "Subscription", 1, "email", "subscription", "all", "email"],
                [9, "Reorder Quickly", 1, "email", "reorder", "all", "email"],
                [9, "Chat Recap", 1, "email", "chat_recap", "app", "email"],
                ["", "Digital Care Chat", 1, "email", "", "web", ""],
                [14, "Chat Recap", 1, "email", "chat_recap", "web", ""],
                ["", "Notify me", 1, "sms", "", "all", "sms"],
                [16, "Unknown Device Sign In", 1, "sms", "unknown_device", "all", "sms"],
                [16, "My Appointments", 1, "sms", "my_appointment", "all", "sms"],
                ["", "Transmart Updates", 1, "push", "update", "app", "push"],
                [19, "Promos & Events", 1, "push", "promo", "app", "push"],
                [19, "Information", 1, "push", "information", "app", "push"],
                ["", "Services", 1, "push", "", "app", "push"],
                [22, "Subscription", 1, "push", "subscription", "app", "push"],
                [22, "Reorder Quickly", 1, "push", "reorder", "app", "push"],

            ];
            $columns = [
               'parent_id',
                'name',
                'default_value',
                'message_type',
                'event_type',
                'area',
                "tab"
            ];
            $this->setup->getConnection()->insertArray($tableName, $columns, $data);
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
