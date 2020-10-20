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
}
