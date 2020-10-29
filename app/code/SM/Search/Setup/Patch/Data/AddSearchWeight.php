<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Search
 *
 * Date: October, 26 2020
 * Time: 10:08 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Search\Setup\Patch\Data;

class AddSearchWeight implements \Magento\Framework\Setup\Patch\DataPatchInterface
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
        $priority = 10;
        $attributeCodes = [
            'name',
            'category_names',
            'sku',
            'shop_by_brand',
        ];

        foreach ($attributeCodes as $code) {
            if ($eav->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $code)) {
                $eav->updateAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $code,
                    [
                        'search_weight' => $priority--
                    ]
                );
            }
        }
    }
}
