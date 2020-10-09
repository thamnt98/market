<?php

declare(strict_types=1);

namespace SM\Reports\Model\ReportEvent;

use Magento\Framework\Exception\LocalizedException;
use Magento\Reports\Model\EventFactory as ReportEventFactory;
use Magento\Reports\Model\ResourceModel\Event as ResourceModel;
use Magento\Store\Model\StoreManagerInterface;

class Creator
{
    /**
     * @var ReportEventFactory
     */
    protected $reportEventFactory;

    /**
     * @var ResourceModel
     */
    protected $resourceModel;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Creator constructor.
     * @param ReportEventFactory $reportEventFactory
     * @param ResourceModel $resourceModel
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ReportEventFactory $reportEventFactory,
        ResourceModel $resourceModel,
        StoreManagerInterface $storeManager
    ) {
        $this->reportEventFactory = $reportEventFactory;
        $this->resourceModel = $resourceModel;
        $this->storeManager = $storeManager;
    }

    /**
     * @param int $eventTypeId
     * @param int $objectId
     * @param int $subjectId
     * @param int $subtype
     * @throws LocalizedException
     */
    public function create(int $eventTypeId, int $objectId, int $subjectId, int $subtype = 0): void
    {
        $entity = $this->reportEventFactory->create();
        $entity->setData([
            'event_type_id' => $eventTypeId,
            'object_id' => $objectId,
            'subject_id' => $subjectId, // customerId / visitorId
            'subtype' => $subtype, // 0: customer, 1: visitor
            'store_id' => $this->storeManager->getStore()->getId(),
        ]);

        $this->resourceModel->save($entity);
    }
}
