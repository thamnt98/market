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
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $directory;

    /**
     * AbstractSync constructor.
     *
     * @param \Magento\Framework\Filesystem                      $filesystem
     * @param \Magento\Framework\App\ResourceConnection          $resourceConnection
     * @param \Magento\Framework\MessageQueue\PublisherInterface $publisher
     * @param \Magento\Framework\Logger\Monolog                  $logger
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\MessageQueue\PublisherInterface $publisher,
        \Magento\Framework\Logger\Monolog $logger
    ) {
        $this->connection = $resourceConnection->getConnection();
        $this->publisher = $publisher;
        $this->logger = $logger;
        $this->directory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
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
     * @return string
     */
    abstract protected function getLockFileName();

    /**
     * @param array $data
     *
     * @return object
     */
    abstract protected function generateObject($data);

    /**
     * Cron execution function.
     */
    public function execute()
    {
        if ($this->isLocked()) {
            return;
        }

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

        $this->unlock();
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
                ['event' => 'sub_event']
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
     * Check cron is locked.
     *
     * @return bool
     */
    protected function isLocked()
    {
        if ($this->directory->isFile($this->getLockFileName())) {
            return true;
        } else {
            $this->directory->openFile($this->getLockFileName());

            return false;
        }
    }

    /**
     * Unlock cron
     */
    protected function unlock()
    {
        try {
            $this->directory->delete($this->getLockFileName());
        } catch (\Exception $e) {
            $this->logger->error(
                "Consumer Sync Failed ('{$this->getQueueTopic()}'):\n\tCan not DELETE lock. " . $e->getMessage()
            );
        }
    }
}
