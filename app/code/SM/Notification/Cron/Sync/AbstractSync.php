<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 14 2020
 * Time: 4:54 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Cron\Sync;

abstract class AbstractSync
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Magento\Framework\MessageQueue\PublisherInterface
     */
    protected $publisher;

    /**
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $logger;

    /**
     * AbstractSync constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection          $resourceConnection
     * @param \Magento\Framework\MessageQueue\PublisherInterface $publisher
     * @param \Magento\Framework\Logger\Monolog                  $logger
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\MessageQueue\PublisherInterface $publisher,
        \Magento\Framework\Logger\Monolog $logger
    ) {
        $this->connection = $resourceConnection->getConnection();
        $this->publisher = $publisher;
        $this->logger = $logger;
    }

    public function execute()
    {
        $records = $this->getRecords();
        $this->updateStatus(array_keys($records));
        foreach ($records as $id => $record) {
            if (empty($record['event']) ||
                empty($record['message_id']) ||
                empty($record['customer_id'])
            ) {
                $this->logger->error(
                    "Consumer Sync Failed ('{$this->getQueueTopic()}') : Missing data.\n\t",
                    $record
                );
                continue;
            }

            $this->syncRecord($record);
        }
    }

    /**
     * @param array $record
     *
     * @return bool
     */
    protected function syncRecord($record)
    {
        try {
            $this->publisher->publish($this->getQueueTopic(), $this->generateObject($record));
            $this->logger->info(
                "Sync to Queue Success ('{$this->getQueueTopic()}') :\n\t",
                $record
            );

            return true;
        } catch (\Exception $e) {
            $this->logger->error(
                "Sync to Queue Failed ('{$this->getQueueTopic()}') :\n\t" . $e->getMessage(),
                $record
            );

            return false;
        }
    }

    /**
     * @return array
     */
    protected function getRecords()
    {
        $select = $this->prepareSelect();

        return $this->connection->fetchAssoc($select);
    }

    /**
     * @return \Magento\Framework\DB\Select
     */
    protected function prepareSelect()
    {
        $mainTable = \SM\Notification\Model\ResourceModel\CustomerMessage::TABLE_NAME;
        $select = $this->connection->select();
        $select->from(['main_table' => $mainTable], ['id', 'message_id', 'customer_id'])
            ->joinInner(
                ['message' => \SM\Notification\Model\ResourceModel\Notification::TABLE_NAME],
                'main_table.message_id = message.id',
                ['event']
            )->where(
                'message.start_date IS NULL OR message.start_date <= NOW()'
            )->where(
                'message.end_date IS NULL OR message.end_date >= NOW()'
            )->group(
                ["main_table.message_id", "main_table.customer_id"]
            )->limit(500);


        return $select;
    }

    /**
     * @param int[] $ids
     */
    abstract protected function updateStatus($ids);

    /**
     * @return string
     */
    abstract protected function getQueueTopic();

    /**
     * @param array $data
     *
     * @return object
     */
    abstract protected function generateObject($data);
}
