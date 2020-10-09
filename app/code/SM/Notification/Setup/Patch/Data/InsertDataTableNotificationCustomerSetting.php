<?php

namespace SM\Notification\Setup\Patch\Data;

class InsertDataTableNotificationCustomerSetting implements \Magento\Framework\Setup\Patch\DataPatchInterface
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
                ["", "transmart_update", "Transmart Updates", 1, "email", "update", "all", "email"],
                ["transmart_update", "promo_event","Promos & Events", 1, "email", "promo", "all", "email"],
                ["transmart_update", "information","Information", 1, "email", "information", "all", "email"],
                ["", "my_order","My Orders", 1, "email", "order_status", "all", "email"],
                ["my_order", "order_status","Order Status", 1, "email", "order_status", "all", "email"],
                ["my_order", "order_status","Order Status", 1, "sms", "order_status", "web", ""],
                ["my_order", "order_status_sign_out","Order Status When Signed Out", 0, "email", "order_status", "web", ""],
                ["my_order", "order_status_sign_out","Order Status When Signed Out", 1, "sms", "order_status", "web", ""],
                ["", "service","Services", 1, "email", "service", "all", "email"],
                ["service", "my_appointment","My Appointments", 1, "email", "my_appointment", "all", "email"],
                ["service", "subscription","Subscription", 1, "email", "subscription", "all", "email"],
                ["service", "reorder","Reorder Quickly", 1, "email", "reorder", "all", "email"],
                ["service", "chat_recap","Chat Recap", 1, "email", "chat_recap", "app", "email"],
                ["", "digital_care_chat","Digital Care Chat", 1, "email", "", "web", ""],
                ["digital_care_chat", "chat_recap","Chat Recap", 1, "email", "chat_recap", "web", ""],
                ["", "notify_me","Notify me", 1, "sms", "", "all", "sms"],
                ["notify_me", "unknown_device","Unknown Device Sign In", 1, "sms", "unknown_device", "all", "sms"],
                ["notify_me", "my_appointment","My Appointments", 1, "sms", "my_appointment", "all", "sms"],
                ["", "transmart_update","Transmart Updates", 1, "push", "update", "app", "push"],
                ["transmart_update", "promo_event","Promos & Events", 1, "push", "promo", "app", "push"],
                ["transmart_update", "information","Information", 1, "push", "information", "app", "push"],
                ["", "service","Services", 1, "push", "", "app", "push"],
                ["service", "subscription","Subscription", 1, "push", "subscription", "app", "push"],
                ["service", "reorder","Reorder Quickly", 1, "push", "reorder", "app", "push"],

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
