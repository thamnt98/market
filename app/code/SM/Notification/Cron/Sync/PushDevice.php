<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 14 2020
 * Time: 5:24 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Cron\Sync;

use SM\Notification\Model\Push as Model;
use SM\Notification\Model\ResourceModel\Push as ResourceModel;

class PushDevice extends AbstractSync
{
    /**
     * @var \SM\Notification\Api\Data\Queue\PushDeviceInterfaceFactory
     */
    protected $objectFactory;

    /**
     * Email constructor.
     *
     * @param \SM\Notification\Api\Data\Queue\PushDeviceInterfaceFactory $objectFactory
     * @param \Magento\Framework\App\ResourceConnection                  $resourceConnection
     * @param \Magento\Framework\MessageQueue\PublisherInterface         $publisher
     * @param \Magento\Framework\Logger\Monolog                          $logger
     */
    public function __construct(
        \SM\Notification\Api\Data\Queue\PushDeviceInterfaceFactory $objectFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\MessageQueue\PublisherInterface $publisher,
        \Magento\Framework\Logger\Monolog $logger
    ) {
        parent::__construct($resourceConnection, $publisher, $logger);
        $this->objectFactory = $objectFactory;
    }

    protected function getQueueTopic()
    {
        return Model::CONSUMER_NAME;
    }

    protected function updateStatus($ids)
    {
        if (empty($ids) || !is_array($ids)) {
            return;
        }

        $this->connection->update(
            \SM\Notification\Model\ResourceModel\CustomerMessage::TABLE_NAME,
            [
                'push_status' => \SM\Notification\Model\Notification::SYNCED,
            ],
            'id IN (' . implode(',', $ids) . ')'
        );
    }

    /**
     * @param array $data
     *
     * @return \SM\Notification\Api\Data\Queue\PushDeviceInterface
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
            ['type' => \SM\Notification\Model\ResourceModel\Push::TABLE_NAME],
            'main_table.message_id = type.message_id',
            ['content', 'title']
        )->where(
            'main_table.push_status = ?',
            \SM\Notification\Model\Notification::SYNC_PENDING
        );

        return $select;
    }
}
