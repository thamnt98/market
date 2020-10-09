<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: May, 02 2020
 * Time: 10:18 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Setup\Patch\Data;

class RemoveFilterCount implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * AddAllFilterListToCategory constructor.
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup
    ) {
        $this->setup = $setup;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        $this->setup->startSetup();

        $this->update();

        $this->setup->endSetup();
    }

    protected function update()
    {
        $table = $this->setup->getTable('core_config_data');
        if (!$this->setup->tableExists($table)) {
            return;
        }

        $conn = $this->setup->getConnection();
        $path = \Magento\Catalog\Helper\Data::XML_PATH_DISPLAY_PRODUCT_COUNT;
        $conn->update(
            $table,
            ['value' => 0],
            "path = '${path}'"
        );
    }
}
