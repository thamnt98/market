<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: July, 16 2020
 * Time: 11:43 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Setup\Patch\Data;

class AmastyTurnOffSpecialCheck implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    const VERSION = '0.0.1';

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * AddGtmConfig constructor.
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
        $tableName = $this->setup->getTable('core_config_data');
        if (!$this->setup->tableExists($tableName)) {
            return;
        }

        $conn = $this->setup->getConnection();
        $path = [
            'amrules/skip_price/skip_special_price_configurable',
            'amrules/skip_price/skip_special_price',
            'amrules/skip_price/skip_tier_price',
        ];
        $conn->delete(
            $tableName,
            'path IN (\''. implode("','", $path) . '\')'
        );

        $data = [
            'scope' => 'default',
            'scope_id' => 0,
            'value' => 0
        ];

        foreach ($path as $item) {
            $data['path'] = $item;
            $conn->insert($tableName, $data);
        }
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
