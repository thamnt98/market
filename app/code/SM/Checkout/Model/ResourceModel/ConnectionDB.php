<?php

namespace SM\Checkout\Model\ResourceModel;

/**
 * Class ConnectionDB
 * @package SM\Checkout\Model\ResourceModel
 */
class ConnectionDB
{
    /**
     * @var array
     */
    protected $tableNames = [];

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $readAdapter;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $writeAdapter;

    /**
     * ConnectionDB constructor.
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
        $this->readAdapter = $this->resourceConnection->getConnection('core_read');
        $this->writeAdapter = $this->resourceConnection->getConnection('core_write');
    }

    /**
     * @param array $items
     * @return string
     */
    public function getQuoteAddressItems($items)
    {
        $select = $this->readAdapter->select()
            ->from(
                [$this->getTableName('quote_address_item')],
                ['quote_address_id', 'quote_item_id']
            )->where('quote_item_id IN (' . implode(",", $items) . ')');
        return $this->readAdapter->fetchAll($select);
    }

    /**
     * @param int $quoteItemId
     * @return array
     */
    public function getAddressId($quoteItemId)
    {
        $select = $this->readAdapter->select()
            ->from(
                [$this->getTableName('quote_address_item')],
                ['quote_address_id']
            )->where('quote_item_id =? ', $quoteItemId);
        $result = $this->readAdapter->fetchCol($select);
        if (isset($result[0])) {
            $select = $this->readAdapter->select()
                ->from(
                    [$this->getTableName('quote_address')],
                    ['customer_address_id', 'customer_id', 'quote_id']
                )
                ->where('address_id = ?', $result[0]);
            return $this->readAdapter->fetchRow($select);
        }
        return false;
    }

    /**
     * @param string $entity
     * @return bool|mixed
     */
    public function getTableName($entity)
    {
        if (!isset($this->tableNames[$entity])) {
            try {
                $this->tableNames[$entity] = $this->resourceConnection->getTableName($entity);
            } catch (\Exception $e) {
                return false;
            }
        }
        return $this->tableNames[$entity];
    }

    /**
     * @param $skuList
     * @param false $limit
     * @param array $limitStoreCodeList
     * @return array
     */
    public function getMsi($skuList, $limit = false, $limitStoreCodeList = [])
    {
        $select = $this->readAdapter->select()
        ->from(
            ['main_table' => $this->getTableName('inventory_source_item')],
            ['source_code', 'quantity', 'sku']
        )
        ->joinLeft(
            ['t1' => 'inventory_source'],
            'main_table.source_code = t1.source_code',
            ['latitude', 'longitude', 'country_id', 'region', 'city', 'street', 'postcode', 'name']
        )
        ->where('main_table.sku IN (?)', $skuList)
        ->where('main_table.status =?', 1)
        ->where('main_table.quantity > 0')
        ->where('t1.enabled =?', 1);
        if ($limit) {
            $select->where('main_table.source_code IN (?)', $limitStoreCodeList);
        }
        return $this->readAdapter->fetchAll($select);
    }

    /**
     * @param $orderId
     */
    public function updateSendOrderConfirmation($orderId)
    {
        $table = $this->getTableName('sales_order');
        if ($table) {
            try {
                $this->writeAdapter->update(
                    $table,
                    ['send_order_confirmation' => 1],
                    ['entity_id' => $orderId]
                );
            } catch (\Exception $e) {
                $e->getMessage();
            }
        }
    }
}