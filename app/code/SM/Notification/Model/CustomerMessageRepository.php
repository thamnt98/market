<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 08 2020
 * Time: 1:45 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model;

class CustomerMessageRepository implements \SM\Notification\Api\CustomerMessageRepositoryInterface
{
    const TYPE_READ   = 'read';
    const TYPE_UNREAD = 'unread';

    /**
     * @var ResourceModel\CustomerMessage
     */
    protected $resourceModel;

    /**
     * @var ResourceModel\CustomerMessage\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var \SM\Notification\Api\CustomerMessageResultInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \SM\Notification\Helper\Data
     */
    protected $helper;

    /**
     * @var \SM\Notification\Api\Data\NotificationTypeInterfaceFactory
     */
    protected $notificationTypeFactory;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $emulation;

    /**
     * CustomerMessageRepository constructor.
     *
     * @param \Magento\Store\Model\App\Emulation                                 $emulation
     * @param \SM\Notification\Api\Data\NotificationTypeInterfaceFactory         $notificationTypeFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                        $dateTime
     * @param \SM\Notification\Api\CustomerMessageResultInterfaceFactory         $searchResultsFactory
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
     * @param \SM\Notification\Helper\Data                                       $helper
     * @param ResourceModel\CustomerMessage                                      $resourceModel
     * @param ResourceModel\CustomerMessage\CollectionFactory                    $collectionFactory
     */
    public function __construct(
        \Magento\Store\Model\App\Emulation $emulation,
        \SM\Notification\Api\Data\NotificationTypeInterfaceFactory $notificationTypeFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \SM\Notification\Api\CustomerMessageResultInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \SM\Notification\Helper\Data $helper,
        \SM\Notification\Model\ResourceModel\CustomerMessage $resourceModel,
        \SM\Notification\Model\ResourceModel\CustomerMessage\CollectionFactory $collectionFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dateTime = $dateTime;
        $this->helper = $helper;
        $this->notificationTypeFactory = $notificationTypeFactory;
        $this->emulation = $emulation;
    }

    /**
     * @param int    $customerId
     * @param int[]  $messageIds
     * @param string $type
     *
     * @return int
     */
    public function updateReadByIds($customerId, $messageIds, $type)
    {
        if (empty($messageIds)) {
            return 0;
        }

        $coll = $this->getCollectionByIds($customerId, $messageIds);

        return $this->updateRead($coll->getAllIds(), $type);
    }

    /**
     * @param int                                                 $customerId
     * @param string                                              $type
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     *
     * @return int
     */
    public function updateReadAll(
        $customerId,
        $type,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null
    ) {
        $coll = $this->getCollection($searchCriteria);
        $coll->getSelect()->where('main_table.customer_id = ?', $customerId);

        return $this->updateRead($coll->getAllIds(), $type);
    }

    /**
     * @param int[]  $customerMessageIds
     * @param string $type
     *
     * @return int
     */
    protected function updateRead($customerMessageIds, $type)
    {
        if (empty($customerMessageIds)) {
            return 0;
        }

        switch ($type) {
            case self::TYPE_READ:
                return $this->resourceModel->read($customerMessageIds);
            case self::TYPE_UNREAD:
                return $this->resourceModel->unRead($customerMessageIds);
            default:
                return 0;
        }
    }

    /**
     * @param int                                            $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param int                                            $isMobile
     *
     * @return \SM\Notification\Api\CustomerMessageResultInterface
     */
    public function getList($customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria, $isMobile = 0)
    {
        $items = [];
        $isApiRequest = $this->helper->isApiRequest();
        $coll = $this->getCollection($searchCriteria);
        $coll->getSelect()->where('main_table.customer_id = ?', $customerId);
        $coll->load();

        /** @var \SM\Notification\Api\CustomerMessageResultInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setTotalCount($coll->getSize());

        if ($isApiRequest) {
            $this->emulation->startEnvironmentEmulation(
                $this->helper->getStoreId(),
                \Magento\Framework\App\Area::AREA_FRONTEND,
                true
            );
        }

        /** @var \SM\Notification\Model\CustomerMessage $item */
        foreach ($coll->getItems() as $item) {
            $dataModel = $item->getDataModel();
            if ($isMobile) {
                $dataModel->setTitle(strip_tags($dataModel->getTitle()));
                $dataModel->setContent(strip_tags($dataModel->getContent()));
            }

            $items[$item->getId()] = $dataModel;
        }

        if ($isApiRequest) {
            $this->emulation->stopEnvironmentEmulation();
        }

        $searchResult->setItems($items);

        return $searchResult;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     *
     * @return \SM\Notification\Model\ResourceModel\CustomerMessage\Collection
     */
    protected function getCollection(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        $eventEnabled = array_keys($this->helper->getEventEnable());
        $time = $this->dateTime->gmtDate('Y-m-d H:i:s');
        /** @var \SM\Notification\Model\ResourceModel\CustomerMessage\Collection $collection */
        $collection = $this->collectionFactory->create();

        if (empty($eventEnabled)) {
            $collection->getSelect()->where('id < 0');

            return $collection;
        }

        $messageAlias = \SM\Notification\Model\ResourceModel\CustomerMessage::MESSAGE_JOIN_TABLE_ALIAS;

        if ($searchCriteria) {
            $this->collectionProcessor->process($searchCriteria, $collection);
        }

        $collection->getSelect()
            ->where(
                "{$messageAlias}.start_date IS NULL OR {$messageAlias}.start_date <= ?",
                $time
            )->where(
                "{$messageAlias}.end_date IS NULL OR {$messageAlias}.end_date >= ?",
                $time
            )->where(
                "{$messageAlias}.event IN ('" . implode("','", $eventEnabled) . "')"
            );

        return $collection;
    }

    /**
     * @param int    $customerId
     * @param array  $messageIds
     *
     * @return \SM\Notification\Model\ResourceModel\CustomerMessage\Collection
     */
    public function getCollectionByIds($customerId, $messageIds = [])
    {
        $coll = $this->getCollection();
        $coll->addFieldToFilter('customer_id', $customerId);

        if ($messageIds) {
            $coll->addFieldToFilter('message_id', ['in' => $messageIds]);
        }

        return $coll;
    }

    /**
     * @param int                                                 $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     *
     * @return int
     */
    public function getCountUnread($customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        $coll = $this->getCollection($searchCriteria);
        $coll->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('is_read', ['neq' => 1]);

        return $coll->getSize();
    }

    /**
     * @return \SM\Notification\Api\Data\NotificationTypeInterface[]
     */
    public function getEnabledEvents()
    {
        $result = [];
        $list = $this->helper->getEventConfig();

        foreach ($list as $item) {
            if ($item['enable']) {

                /** @var \SM\Notification\Api\Data\NotificationTypeInterface $type */
                $type = $this->notificationTypeFactory->create();
                $type->setName($item['name'])->setValue($item['event_type']);
                $result[$item['event_type']] = $type;
            }
        }

        return $result;
    }
}
