<?php
/**
 * @category Trans
 * @package  Trans_IntegrationNotification
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationNotification\Model;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;

use Trans\IntegrationNotification\Api\IntegrationNotificationLogRepositoryInterface;
use Trans\IntegrationNotification\Api\Data\IntegrationNotificationLogInterface;
use Trans\IntegrationNotification\Api\Data\IntegrationNotificationLogInterfaceFactory;
use Trans\IntegrationNotification\Model\ResourceModel\IntegrationNotificationLog as ResourceModel;

/**
 * Class IntegrationNotificationLogRepository
 */
class IntegrationNotificationLogRepository implements IntegrationNotificationLogRepositoryInterface
{
    /**
     * @var array
     */
    protected $instances = [];
    
    /**
     * @var ResourceModel
     */
    protected $resource;
    
    /**
     * @var IntegrationNotificationLogInterfaceFactory
     */
    protected $logInterfaceFactory;
    /**
     * @var IntegrationNotificationLogInterface
     */
    protected $logInterface;
    
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @param ResourceModel $resourceModel
     * @param IntegrationNotificationLogInterfaceFactory $logInterfaceFactory
     * @param IntegrationNotificationLogInterface $logInterface
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceModel $resource,
        IntegrationNotificationLogInterfaceFactory $logInterfaceFactory,
        IntegrationNotificationLogInterface $logInterface,
        DataObjectHelper $dataObjectHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->logInterfaceFactory = $logInterfaceFactory;
        $this->logInterface = $logInterface;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(IntegrationNotificationLogInterface $data)
    {
        /** @var IntegrationNotificationLogInterface|\Magento\Framework\Model\AbstractModel $data */
        try {
            $this->resource->save($data);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the data: %1',
                $exception->getMessage()
            ));
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(IntegrationNotificationLogInterface $integrationNotificationLog)
    {
        /** @var \Trans\IntegrationNotification\Api\Data\IntegrationNotificationLogInterface|\Magento\Framework\Model\AbstractModel $integrationNotificationLog */
        $integrationNotificationLogId = $integrationNotificationLog->getId();
        try {
            unset($this->instances[$integrationNotificationLogId]);
            $this->resource->delete($integrationNotificationLog);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove data %1', $integrationNotificationLogId)
            );
        }
        unset($this->instances[$integrationNotificationLogId]);
        return true;
    }
}
