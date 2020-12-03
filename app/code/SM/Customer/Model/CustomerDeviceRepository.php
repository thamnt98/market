<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Customer
 *
 * Date: June, 08 2020
 * Time: 2:44 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Customer\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class CustomerDeviceRepository implements \SM\Customer\Api\CustomerDeviceRepositoryInterface
{
    /**
     * @var ResourceModel\CustomerDevice
     */
    protected $resource;

    /**
     * @var CustomerDeviceFactory
     */
    protected $modelFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \SM\Customer\Api\Data\CustomerDeviceInterfaceFactory
     */
    protected $dataModelFactory;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $dataConverter;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var ResourceModel\CustomerDevice\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \SM\Customer\Api\Data\CustomerDeviceSearchResultInterfaceFactory
     */
    protected $searchResultFactory;

    /**
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $logger;

    /**
     * CustomerDeviceRepository constructor.
     *
     * @param CustomerDeviceFactory                                              $modelFactory
     * @param ResourceModel\CustomerDevice                                       $resource
     * @param \Magento\Framework\Logger\Monolog                                  $logger
     * @param \Magento\Framework\Api\DataObjectHelper                            $dataObjectHelper
     * @param ResourceModel\CustomerDevice\CollectionFactory                     $collectionFactory
     * @param \SM\Customer\Api\Data\CustomerDeviceInterfaceFactory               $dataModelFactory
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter               $extensibleDataObjectConverter
     * @param \SM\Customer\Api\Data\CustomerDeviceSearchResultInterfaceFactory   $searchResultFactory
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        CustomerDeviceFactory $modelFactory,
        ResourceModel\CustomerDevice $resource,
        \Magento\Framework\Logger\Monolog $logger,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        ResourceModel\CustomerDevice\CollectionFactory $collectionFactory,
        \SM\Customer\Api\Data\CustomerDeviceInterfaceFactory $dataModelFactory,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        \SM\Customer\Api\Data\CustomerDeviceSearchResultInterfaceFactory $searchResultFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->modelFactory = $modelFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataModelFactory = $dataModelFactory;
        $this->dataConverter = $extensibleDataObjectConverter;
        $this->collectionProcessor = $collectionProcessor;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->logger = $logger;
    }

    /**
     * @param \SM\Customer\Api\Data\CustomerDeviceInterface $device
     *
     * @return \SM\Customer\Api\Data\CustomerDeviceInterface
     * @throws CouldNotSaveException
     */
    public function save(\SM\Customer\Api\Data\CustomerDeviceInterface $device)
    {
        $this->logger->info('--- Register device -----------------');
        /** @var CustomerDevice $model */
        $model = $this->modelFactory->create();
        $model->setData('customer_id', $device->getCustomerId())
            ->setData('device_id', $device->getDeviceId())
            ->setData('type', $device->getType())
            ->setData('token', $device->getToken());
        try {
            $this->resource->save($model);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
            throw new CouldNotSaveException(__('Could not save the device: %1', $e->getMessage()));
        }
        $this->logger->info('--- Register done -----------------');

        return $this->convertDataModel($model);
    }

    /**
     * @param string $id
     *
     * @return \SM\Customer\Api\Data\CustomerDeviceInterface
     * @throws NoSuchEntityException
     */
    public function get($id)
    {
        /** @var CustomerDevice $model */
        $model = $this->modelFactory->create();
        $this->resource->load($model, $id);
        if (!$model->getId()) {
            throw new NoSuchEntityException(__('Device with id "%1" does not exist.', $id));
        }

        return $this->convertDataModel($model);
    }

    public function getByDeviceId($deviceId, $customerId)
    {
        /** @var ResourceModel\CustomerDevice\Collection $coll */
        $coll = $this->collectionFactory->create();
        $coll->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('device_id', $deviceId);
        /** @var CustomerDevice $model */
        $model = $coll->getFirstItem();
        if (!$model->getId()) {
            throw new NoSuchEntityException(__('Device with device id "%1" does not exist.', $deviceId));
        }

        return $this->convertDataModel($model);
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \SM\Customer\Api\Data\CustomerDeviceSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var ResourceModel\CustomerDevice\Collection $coll */
        $coll = $this->collectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $coll);
        /** @var \SM\Customer\Api\Data\CustomerDeviceSearchResultInterface $result */
        $result = $this->searchResultFactory->create();
        $result->setSearchCriteria($searchCriteria);
        $result->setTotalCount($coll->getSize());
        $items = [];
        foreach ($coll as $item) {
            $items[] = $this->convertDataModel($item);
        }

        return $result->setItems($items);
    }

    /**
     * @param \SM\Customer\Api\Data\CustomerDeviceInterface $device
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(\SM\Customer\Api\Data\CustomerDeviceInterface $device)
    {
        try {
            $device->setStatus(\SM\Customer\Api\Data\CustomerDeviceInterface::STATUS_DISABLE);
            $this->save($device);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete the Device: %1', $exception->getMessage()));
        }

        return true;
    }

    /**
     * @param string $id
     *
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($id)
    {
        return $this->delete($this->get($id));
    }

    /**
     * @param string $deviceId
     * @param int    $customerId
     *
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteByDeviceId($deviceId, $customerId)
    {
        return $this->delete($this->getByDeviceId($deviceId, $customerId));
    }

    /**
     * @param CustomerDevice $model
     *
     * @return \SM\Customer\Api\Data\CustomerDeviceInterface
     */
    protected function convertDataModel($model)
    {
        /** @var \SM\Customer\Api\Data\CustomerDeviceInterface $result */
        $result = $this->dataModelFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $result,
            $model->getData(),
            \SM\Customer\Api\Data\CustomerDeviceInterface::class
        );

        return $result;
    }
}
