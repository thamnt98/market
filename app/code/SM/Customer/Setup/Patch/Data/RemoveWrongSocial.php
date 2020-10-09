<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Customer
 *
 * Date: May, 06 2020
 * Time: 2:06 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Customer\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\PatchInterface;

class RemoveWrongSocial implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * RemoveWrongSocial constructor.
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     */
    public function __construct(\Magento\Framework\Setup\ModuleDataSetupInterface $setup)
    {
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

        $conn = $this->setup->getConnection();
        $table = $this->setup->getTable('mageplaza_social_customer');
        if ($this->setup->tableExists($table)) {
            $conn->delete(
                $table,
                'social_id IS NULL'
            );
        }

        $this->setup->endSetup();
    }
}
