<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: November, 02 2020
 * Time: 4:33 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Setup\Patch\Data;

class ResetCronConfig implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup
    ) {
        $this->setup = $setup;
    }

    public function apply()
    {
        $this->setup->startSetup();

        $table = $this->setup->getTable('core_config_data');
        if ($this->setup->tableExists($table)) {
            $cronPath = [
                'sm_notification/generate/limit_stock',
                'sm_notification/generate/abandoned_cart',
                'sm_notification/generate/abandoned_cart_repeat',
                'sm_notification/generate/remind_pickup_cron',
                'sm_notification/sync/push_device',
                'sm_notification/sync/sms',
                'sm_notification/sync/email',
            ];
            $this->setup->getConnection()->delete(
                $table,
                "path IN ('" . implode("','", $cronPath) . "')"
            );
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
