<?php

namespace SM\Notification\Setup\Patch\Data;

class UpdateRemoveSettingConfig implements \Magento\Framework\Setup\Patch\DataPatchInterface
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
            $this->setup->getConnection()->delete($this->setup->getTable('sm_notification_customer_setting'),
                "code = 'my_appointment'");
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
