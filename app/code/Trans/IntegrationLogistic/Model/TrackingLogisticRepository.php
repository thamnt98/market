<?php
/**
 * @category Trans
 * @package  Trans_IntegrationLogistic
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2020 PT CT CORP DIGITAL. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationLogistic\Model;

use Magento\Framework\Exception\LocalizedException as Exception;
use Magento\Framework\Message\ManagerInterface;
use Trans\IntegrationLogistic\Api\Data\TrackingLogisticInterface;
use Trans\IntegrationLogistic\Api\Data\TrackingLogisticInterfaceFactory;
use Trans\IntegrationLogistic\Api\TrackingLogisticRepositoryInterface;
use Trans\IntegrationLogistic\Model\ResourceModel\TrackingLogistic as TrackingLogisticResourceModel;
use Trans\IntegrationLogistic\Model\ResourceModel\TrackingLogistic\CollectionFactory;

/**
 * Class TrackingLogisticRepository
 */
class TrackingLogisticRepository implements TrackingLogisticRepositoryInterface
{

    /**
     * @var array
     */
    private $instances = [];

    /**
     * @var TrackingLogisticResourceModel
     */
    private $trackingLogisticResourceModel;

    /**
     * @var TrackingLogisticInterface
     */
    private $trackingLogisticInterface;

    /**
     * @var TrackingLogisticInterfaceFactory
     */
    private $trackingLogisticInterfaceFactory;

    private $collectionFactory;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * TrackingLogisticRepository constructor.
     * @param TrackingLogisticResourceModel $trackingLogisticResourceModel
     * @param TrackingLogisticInterface $trackingLogisticInterface
     * @param TrackingLogisticInterfaceFactory $trackingLogisticInterfaceFactory
     * @param CollectionFactory $collectionFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        TrackingLogisticResourceModel $trackingLogisticResourceModel,
        TrackingLogisticInterface $trackingLogisticInterface,
        TrackingLogisticInterfaceFactory $trackingLogisticInterfaceFactory,
        CollectionFactory $collectionFactory,
        ManagerInterface $messageManager
    ) {
        $this->trackingLogisticResourceModel    = $trackingLogisticResourceModel;
        $this->trackingLogisticInterface        = $trackingLogisticInterface;
        $this->trackingLogisticInterfaceFactory = $trackingLogisticInterfaceFactory;
        $this->collectionFactory                = $collectionFactory;
        $this->messageManager                   = $messageManager;
    }

    /**
     * @param TrackingLogisticInterface $trackingLogisticInterface
     * @return TrackingLogisticInterface
     * @throws \Exception
     */
    public function save(TrackingLogisticInterface $trackingLogisticInterface)
    {
        try {
            $this->trackingLogisticResourceModel->save($trackingLogisticInterface);
        } catch (Exception $e) {
            $this->messageManager
                ->addExceptionMessage(
                    $e,
                    'There was a error while saving the data  ' . $e->getMessage()
                );
        }

        return $trackingLogisticInterface;
    }

    /**
     * Retrieve data.
     *
     * @param int $trackingId
     * @return \Trans\IntegrationLogistic\Api\Data\TrackingLogisticInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($trackingId)
    {
        if (!isset($this->instances[$trackingId])) {
            /** @var \Trans\IntegrationLogistic\Api\Data\TrackingLogisticInterface|\Magento\Framework\Model\AbstractModel $track */
            $track = $this->trackingLogisticInterface->create();
            $this->trackingLogisticResourceModel->load($track, $trackingId);
            if (!$track->getTrackingId()) {
                throw new NoSuchEntityException(__('Data Tracking doesn\'t exist'));
            }
            $this->instances[$trackingId] = $track;
        }
        return $this->instances[$trackingId];
    }

    /**
     * @param TrackingLogisticInterface $trackingLogisticInterface
     * @return bool
     * @throws \Exception
     */
    public function delete(TrackingLogisticInterface $trackingLogisticInterface)
    {
        $id = $trackingLogisticInterface->getTrackingId();
        try {
            unset($this->instances[$id]);
            $this->trackingLogisticResourceModel->delete($trackingLogisticInterface);
        } catch (Exception $e) {
            $this->messageManager
                ->addExceptionMessage($e, 'There was a error while deleting the data');
        }
        unset($this->instances[$id]);
        return true;
    }
}
