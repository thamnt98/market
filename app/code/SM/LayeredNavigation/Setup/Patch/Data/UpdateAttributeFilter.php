<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: May, 20 2020
 * Time: 9:01 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Setup\Patch\Data;

class UpdateAttributeFilter implements \Magento\Framework\Setup\Patch\DataPatchInterface
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
     * @var \SM\LayeredNavigation\Helper\Data\FilterList
     */
    protected $filterListHelper;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    protected $eavAttribute;

    /**
     * Constructor.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute
     * @param \SM\LayeredNavigation\Helper\Data\FilterList $filterListHelper
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute,
        \SM\LayeredNavigation\Helper\Data\FilterList $filterListHelper,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup
    ) {
        $this->setup = $setup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->filterListHelper = $filterListHelper;
        $this->eavAttribute = $eavAttribute;
    }

    public function apply()
    {
        $this->setup->startSetup();

        try {
            $this->updateAttributeToFilter();
            $this->updateAmastyFilterShowMore();
            $this->updateAmastyFilterSetting();
            $this->updateSystemConfig();
        } catch (\Exception $e) {
        }

        $this->setup->endSetup();
    }

    protected function updateAttributeToFilter()
    {
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->setup]);
        if ($this->eavAttribute->getIdByCode(\Magento\Catalog\Model\Product::ENTITY, 'is_warehouse')) {
            $eavSetup->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'is_warehouse',
                [
                    'is_filterable' => true,
                    'is_searchable' => true
                ]
            );
        }

        if ($this->eavAttribute->getIdByCode(\Magento\Catalog\Model\Product::ENTITY, 'shop_by_brand')) {
            $eavSetup->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'shop_by_brand',
                [
                    'is_filterable' => true,
                    'is_searchable' => true,
                    'frontend_label' => 'Brand'
                ]
            );
        }
    }

    public function updateAmastyFilterSetting()
    {
        $table = $this->setup->getTable('amasty_amshopby_filter_setting');
        if (!$this->setup->tableExists($table)) {
            return;
        }

        $conn = $this->setup->getConnection();
        $select = $conn->select()
            ->from($table, 'setting_id')
            ->where('filter_code = ?', 'attr_category_ids');

        $id = $conn->fetchOne($select);
        if ($id) {
            $conn->update(
                $table,
                ['is_multiselect' => 1],
                "setting_id = {$id}"
            );
        } else {
            $conn->insert(
                $table,
                [
                    'filter_code' => 'attr_category_ids',
                    'is_multiselect' => 1,
                    'visible_in_categories' => 'visible_everywhere'
                ]
            );
        }

        $select = $conn->select()
            ->from($table, 'setting_id')
            ->where('filter_code = ?', 'attr_shop_by_brand');

        $id = $conn->fetchOne($select);
        if ($id) {
            $conn->update(
                $table,
                [
                    'is_multiselect' => 1,
                    'is_show_search_box' => 1,
                    'display_mode' => 0
                ],
                "setting_id = {$id}"
            );
        } else {
            $conn->insert(
                $table,
                [
                    'filter_code' => 'attr_shop_by_brand',
                    'is_multiselect' => 1,
                    'is_show_search_box' => 1,
                    'display_mode' => 0,
                    'visible_in_categories' => 'visible_everywhere'
                ]
            );
        }

        $select = $conn->select()
            ->from($table, 'setting_id')
            ->where('filter_code = ?', 'attr_price');

        $id = $conn->fetchOne($select);
        if ($id) {
            $conn->update(
                $table,
                [
                    'add_from_to_widget' => 1
                ],
                "setting_id = {$id}"
            );
        } else {
            $conn->insert(
                $table,
                [
                    'filter_code' => 'attr_price',
                    'add_from_to_widget' => 1,
                    'visible_in_categories' => 'visible_everywhere'
                ]
            );
        }
    }

    protected function updateAmastyFilterShowMore()
    {
        $table = $this->setup->getTable('amasty_amshopby_filter_setting');
        if (!$this->setup->tableExists($table)) {
            return;
        }

        $conn = $this->setup->getConnection();
        $field = 'number_unfolded_options';
        foreach ($this->filterListHelper->getAllOptions(true) as $option) {
            if (empty($option['attribute_code'])) {
                continue;
            }

            $select = $conn->select()
                ->from($table, 'setting_id')
                ->where('filter_code = ?', 'attr_' . $option['attribute_code']);

            $id = $conn->fetchOne($select);
            $fieldData = [];
            if (strpos($option['attribute_code'], 'price') === false) {
                $fieldData = ['is_multiselect' => 1];
            }

            if ($id) {
                $conn->update(
                    $table,
                    array_merge_recursive([$field => 10], $fieldData),
                    "setting_id = {$id}"
                );
            } else {
                $conn->insert(
                    $table,
                    array_merge_recursive(
                        $fieldData,
                        [
                            'filter_code' => 'attr_' . $option['attribute_code'],
                            'visible_in_categories' => 'visible_everywhere',
                            $field => 10
                        ]
                    )
                );
            }
        }
    }

    protected function updateSystemConfig()
    {
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

        $conn->update(
            $table,
            ['value' => 1],
            "path IN ('" . implode("','", $paths) . "')"
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
