<?php

namespace SM\Sales\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\ResourceModel\Order\Status\Collection as StatusCollection;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory as StatusCollectionFactory;

/**
 * Class StatusState
 * @package SM\Sales\Helper
 */
class StatusState
{
    /**
     * @var StatusCollectionFactory
     */
    protected $statusCollectionFactory;

    private $orderStatus;

    /**
     * StatusState constructor.
     * @param StatusCollectionFactory $statusCollectionFactory
     */
    public function __construct(
        StatusCollectionFactory $statusCollectionFactory
    ) {
        $this->statusCollectionFactory = $statusCollectionFactory;
    }

    /**
     * @param string $status
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getState($status)
    {
        if (is_null($this->orderStatus)) {
            /** @var StatusCollection $statusCollection */
            $statusCollection = $this->statusCollectionFactory
                ->create()
                ->joinStates();
            /** @var Status $statusItem */
            foreach ($statusCollection as $statusItem) {
                $this->orderStatus[$statusItem->getStatus()] = $statusItem->getState();
            }
        }
        if (isset($this->orderStatus[$status])) {
            return $this->orderStatus[$status];
        } else {
            throw new NoSuchEntityException(__("Status %1 is not exists", $status));
        }
    }
}
