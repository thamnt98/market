<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Search
 *
 * Date: June, 02 2020
 * Time: 10:09 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Search\Model\Adapter\Mysql;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;

class TemporaryStorage extends \Magento\Framework\Search\Adapter\Mysql\TemporaryStorage
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Framework\App\DeploymentConfig
     */
    protected $config;

    /**
     * @var \SM\Search\Helper\ProductList\Sort
     */
    protected $sortHelper;

    /**
     * @param \SM\Search\Helper\ProductList\Sort           $sortHelper
     * @param \Magento\Framework\App\ResourceConnection    $resource
     * @param \Magento\Framework\App\DeploymentConfig|null $config
     */
    public function __construct(
        \SM\Search\Helper\ProductList\Sort $sortHelper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\DeploymentConfig $config
    ) {
        parent::__construct($resource, $config);
        $this->resource = $resource;
        $this->config = $config;
        $this->sortHelper = $sortHelper;
    }

    /**
     * Stores Api type Documents
     *
     * @param \Magento\Framework\Api\Search\DocumentInterface[] $documents
     *
     * @return Table
     * @throws \Zend_Db_Exception
     */
    public function storeApiDocuments($documents)
    {
        $data = [];
        foreach ($documents as $document) {
            $item = [
                $document->getId(),
                $document->getCustomAttribute('score')->getValue(),
            ];

            $sort = $this->getSortField();
            if ($sort && $document->getCustomAttribute($sort)) {
                $item[] = $document->getCustomAttribute($sort)->getValue();
            }

            $data[] = $item;
        }

        return $this->populateTemporaryTable($this->createTemporaryTable(), $data);
    }

    /**
     * Populates temporary table
     *
     * @param Table $table
     * @param array $data
     *
     * @return Table
     * @throws \Zend_Db_Exception
     */
    protected function populateTemporaryTable(Table $table, $data)
    {
        if (count($data)) {
            $fields = [
                self::FIELD_ENTITY_ID,
                self::FIELD_SCORE,
            ];

            if ($sort = $this->getSortField()) {
                $fields[] = $sort;
            }

            $this->getConnection()->insertArray($table->getName(), $fields, $data);
        }

        return $table;
    }

    /**
     * Get connection.
     *
     * @return false|AdapterInterface
     */
    protected function getConnection()
    {
        return $this->resource->getConnection();
    }

    /**
     * Create temporary table for search select results.
     *
     * @return Table
     * @throws \Zend_Db_Exception
     */
    protected function createTemporaryTable()
    {
        $connection = $this->getConnection();
        $tableName = $this->resource->getTableName(str_replace('.', '_', uniqid(self::TEMPORARY_TABLE_PREFIX, true)));
        $table = $connection->newTable($tableName);
        if ($this->config->get('db/connection/indexer/persistent')) {
            $connection->dropTemporaryTable($table->getName());
        }
        $table->addColumn(
            self::FIELD_ENTITY_ID,
            Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity ID'
        );
        $table->addColumn(
            self::FIELD_SCORE,
            Table::TYPE_DECIMAL,
            [32, 16],
            ['unsigned' => true, 'nullable' => true],
            'Score'
        );

        if ($sort = $this->getSortField()) {
            if ($this->sortHelper->isDecimalField()) {
                $table->addColumn(
                    $sort,
                    Table::TYPE_DECIMAL,
                    [32, 16],
                    ['unsigned' => true, 'nullable' => true]
                );
            } else {
                $table->addColumn(
                    $sort,
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true]
                );
            }
        }

        $table->setOption('type', 'memory');
        $connection->createTemporaryTable($table);

        return $table;
    }

    /**
     * @return string
     */
    protected function getSortField()
    {
        return $this->sortHelper->getFieldName();
    }
}
