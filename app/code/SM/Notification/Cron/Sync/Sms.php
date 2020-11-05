<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 14 2020
 * Time: 5:59 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Cron\Sync;

class Sms extends AbstractSync
{
    /**
     * @var \SM\Notification\Api\Data\Queue\SmsInterfaceFactory
     */
    protected $objectFactory;

    /**
     * Email constructor.
     *
     * @param \Magento\Framework\Filesystem                       $filesystem
     * @param \SM\Notification\Api\Data\Queue\SmsInterfaceFactory $objectFactory
     * @param \Magento\Framework\App\ResourceConnection           $resourceConnection
     * @param \Magento\Framework\MessageQueue\PublisherInterface  $publisher
     * @param \Magento\Framework\Logger\Monolog                   $logger
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \SM\Notification\Api\Data\Queue\SmsInterfaceFactory $objectFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\MessageQueue\PublisherInterface $publisher,
        \Magento\Framework\Logger\Monolog $logger
    ) {
        parent::__construct($filesystem, $resourceConnection, $publisher, $logger);
        $this->objectFactory = $objectFactory;
    }

    protected function updateStatus($ids)
    {
        if (empty($ids) || !is_array($ids)) {
            return;
        }

        $this->connection->update(
            \SM\Notification\Model\ResourceModel\CustomerMessage::TABLE_NAME,
            [
                'sms_status' => \SM\Notification\Model\Notification::SYNCED,
            ],
            'id IN (' . implode(',', $ids) . ')'
        );
    }

    protected function getQueueTopic()
    {
        return \SM\Notification\Model\Sms::CONSUMER_NAME;
    }

    /**
     * @param array $data
     *
     * @return \SM\Notification\Api\Data\Queue\SmsInterface
     */
    protected function generateObject($data)
    {
        $object = $this->objectFactory->create();
        $object->setData($data);

        return $object;
    }

    /**
     * @override
     * @return \Magento\Framework\DB\Select
     */
    protected function prepareSelect()
    {
        $select = parent::prepareSelect();

        $select->joinInner(
            ['type' => \SM\Notification\Model\ResourceModel\Sms::TABLE_NAME],
            'main_table.message_id = type.message_id',
            ['content']
        )->where(
            'main_table.sms_status = ?',
            \SM\Notification\Model\Notification::SYNC_PENDING
        );

        return $select;
    }

    /**
     * @return string
     */
    protected function getLockFileName()
    {
        return 'sm_notification_sync_sms.lock';
    }
}
