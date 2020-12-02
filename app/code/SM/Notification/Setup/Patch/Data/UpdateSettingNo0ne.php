<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: November, 18 2020
 * Time: 2:54 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Setup\Patch\Data;

class UpdateSettingNo0ne implements \Magento\Framework\Setup\Patch\DataPatchInterface
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

        $this->update();

        $this->setup->endSetup();
    }

    protected function update()
    {
        $table = $this->setup->getTable('sm_notification_customer_setting');
        if (!$this->setup->tableExists($table)) {
            return;
        }

        $conn = $this->setup->getConnection();
        $conn->update(
            $table,
            [
                'code' => 'order_status_sign_out',
                'area' => 'app',
            ],
            "parent_code = 'notify_me' AND code = 'order_status_when_sign_out'"
        );
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
