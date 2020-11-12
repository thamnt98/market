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
     * CustomerDeviceRepository constructor.
     *
     * @param ResourceModel\CustomerDevice                                       $resource
     * @param CustomerDeviceFactory                                              $modelFactory
     * @param \Magento\Framework\Api\DataObjectHelper                            $dataObjectHelper
     * @param ResourceModel\CustomerDevice\CollectionFactory                     $collectionFactory
     * @param \SM\Customer\Api\Data\CustomerDeviceInterfaceFactory               $dataModelFactory
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter               $extensibleDataObjectConverter
     * @param \SM\Customer\Api\Data\CustomerDeviceSearchResultInterfaceFactory   $searchResultFactory
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceModel\CustomerDevice $resource,
        CustomerDeviceFactory $modelFactory,
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
    }

    /**
     * @param \SM\Customer\Api\Data\CustomerDeviceInterface $device
     *
     * @return \SM\Customer\Api\Data\CustomerDeviceInterface
     * @throws CouldNotSaveException
     */
    public function save(\SM\Customer\Api\Data\CustomerDeviceInterface $device)
    {
        $this->removeDuplicateToken($device->getToken(), $device->getDeviceId());
        /** @var CustomerDevice $model */
        $model = $this->modelFactory->create();
        $model->setData('customer_id', $device->getCustomerId())
            ->setData('device_id', $device->getDeviceId())
            ->setData('type', $device->getType())
            ->setData('token', $device->getToken());
        try {
            $this->resource->save($model);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save the device: %1', $e->getMessage()));
        }

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
            /** @var CustomerDevice $model */
            $model = $this->modelFactory->create();
            $this->resource->load($model, $device->getId());
            $this->resource->delete($model);
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

    /**
     * @param string $token
     * @param string $deviceId
     */
    protected function removeDuplicateToken($token, $deviceId)
    {
        /** @var ResourceModel\CustomerDevice\Collection $coll */
        $coll = $this->collectionFactory->create();
        $conn = $coll->getConnection();
        $conn->delete(
            ResourceModel\CustomerDevice::TABLE_NAME,
            "token = '{$token}' OR device_id = '{$deviceId}'"
        );
    }
}
