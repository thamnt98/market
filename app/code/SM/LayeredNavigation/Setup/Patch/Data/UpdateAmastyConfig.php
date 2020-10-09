<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: May, 22 2020
 * Time: 2:24 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Setup\Patch\Data;

class UpdateAmastyConfig implements \Magento\Framework\Setup\Patch\DataPatchInterface
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
        if (!$this->setup->tableExists($table)) {
            return;
        }

        $conn = $this->setup->getConnection();
        $paths = [
            'amshopby/rating_filter/enabled',
            'amshopby/stock_filter/enabled',
            'amshopby/am_is_new_filter/enabled',
            'amshopby/am_on_sale_filter/enabled',
        ];

        foreach ($paths as $path) {
            $data = [
                'path'  => $path,
                'value' => 1
            ];

            $conn->insertOnDuplicate($table, $data, ['value']);
        }

        $conn->insertOnDuplicate(
            $table,
            [
                'path'  => 'amshopby/rating_filter/label',
                'value' => 'Customer Rating'
            ],
            ['value']
        );

        $conn->insertOnDuplicate(
            $table,
            [
                'path'  => 'amshopby/am_on_sale_filter/label',
                'value' => 'Discount & Offer'
            ],
            ['value']
        );

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
