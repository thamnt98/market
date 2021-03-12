<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Search
 *
 * Date: January, 21 2021
 * Time: 6:14 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Search\Setup\Patch\Data;

use Magento\Framework\DB\Ddl\Table;

class UpdateProductCategories implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param \Psr\Log\LoggerInterface                          $logger
     * @param \Magento\Eav\Model\Config                         $eavConfig
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup
    ) {
        $this->setup     = $setup;
        $this->eavConfig = $eavConfig;
        $this->logger = $logger;
    }

    /**
     * @throws \Zend_Db_Exception
     */
    public function apply()
    {
        $this->setup->startSetup();

        $this->update();

        $this->setup->endSetup();
    }

    /**
     * @throws \Zend_Db_Exception
     */
    public function update()
    {
        $this->logger->debug('------------------ Update Product Category Names -------------------');
        $conn = $this->setup->getConnection();
        try {
            $nameAttr    = $this->eavConfig->getAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'name'
            );
            $catNameAttr = $this->eavConfig->getAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'category_names'
            );
        } catch (\Exception $e) {
            $this->logger->debug('Attribute does not exists.');
            $this->logger->debug('------------------  END -------------------');
            
            return;
        }

        $select = $conn
            ->select()
            ->from(['ccp' => 'catalog_category_product'], [])
            ->joinInner(['cce' => 'catalog_category_entity'], 'cce.entity_id = ccp.category_id', [])
            ->joinInner(['cpe' => 'catalog_product_entity'], 'cpe.entity_id = ccp.product_id', [])
            ->group('cpe.row_id')
            ->columns([
                'cpe.row_id as pid',
                "group_concat(replace(cce.path, '/', ',')) as path"
            ]);

        $this->logger->debug('TEMP select', [$select->__toString()]);
        
        $tempTableData = [];
        $tempTable = $conn->newTable('sm_category_product_temp');

        $tempTable->addColumn(
            'pid',
            Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false],
        )->addColumn(
            'cid',
            Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false],
        );

        $conn->createTemporaryTable($tempTable);

        foreach ($conn->fetchAll($select) as $row) {
            foreach (array_unique(explode(',', $row['path'])) as $catId) {
                if ($catId != 0 && $catId != 1) {
                    $tempTableData[] = [
                        $row['pid'],
                        $catId
                    ];
                }
            }
        }

        $conn->insertArray($tempTable->getName(), ['pid', 'cid'], $tempTableData);

        $select = $conn
            ->select()
            ->from(['cp' => $tempTable->getName()], [])
            ->joinInner(['ccev' => 'catalog_category_entity_varchar'], 'cp.cid = ccev.row_id', [])
            ->joinInner(['cce' => 'catalog_category_entity'], 'cce.row_id = ccev.row_id', [])
            ->where('cce.parent_id NOT IN (0, 1)')
            ->where('ccev.attribute_id = ?', $nameAttr->getId())
            ->group('cp.pid')
            ->columns([
                'cp.pid as product_id',
                "group_concat(DISTINCT replace(ccev.value, ',', '|||')) as categories"
            ]);

        $data = $conn->fetchAll($select);
        if (empty($data)) {
            $this->logger->debug('Empty data');
            $this->logger->debug('Query', [$select->__toString()]);
            $this->logger->debug('------------------  END -------------------');
            $conn->dropTemporaryTable($tempTable->getName());

            return;
        }

        $sql  = "INSERT INTO {$catNameAttr->getBackendTable()} (row_id, attribute_id, value) VALUES ";

        foreach ($data as $index => $row) {
            $cats = str_replace(',', ' | ', $row['categories']);
            $cats = str_replace('|||', ',', $cats);
            $cats = str_replace("'", "\'", $cats);
            $sql  .= " ({$row['product_id']}, {$catNameAttr->getId()}, '{$cats}')";

            if ($index < (count($data) - 1)) {
                $sql .= ',';
            }
        }

        $sql .= ' ON DUPLICATE KEY UPDATE value=VALUES(value)';

        $conn->query($sql);
        $conn->dropTemporaryTable($tempTable->getName());
        
        $this->logger->debug('Success', [count($data)]);
        $this->logger->debug('------------------  END -------------------');
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
