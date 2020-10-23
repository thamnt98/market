<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Category
 *
 * Date: October, 22 2020
 * Time: 2:23 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Category\Setup\Patch\Data;

class DisableSearchAttributes implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * AddAllFilterListToCategory constructor.
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Eav\Setup\EavSetupFactory                $eavSetupFactory
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    ) {
        $this->setup = $setup;
        $this->eavSetupFactory = $eavSetupFactory;
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
        /** @var \Magento\Eav\Setup\EavSetup $eav */
        $eav = $this->eavSetupFactory->create(['setup' => $this->setup]);
        if ($eav->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'url_key')) {
            $eav->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'url_key',
                [
                    'is_searchable' => false
                ]
            );
        }
    }
}

