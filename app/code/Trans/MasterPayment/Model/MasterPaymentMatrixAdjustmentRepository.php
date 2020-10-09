<?php
/**
 * @category Trans
 * @package  Trans_MasterPayment
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\MasterPayment\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Store\Model\StoreManagerInterface;
use Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterface;
use Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterfaceFactory;
use Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentSearchResultsInterfaceFactory;
use Trans\MasterPayment\Api\MasterPaymentMatrixAdjustmentRepositoryInterface;
use Trans\MasterPayment\Model\ResourceModel\MasterPaymentMatrixAdjustment as Resource;
use Trans\MasterPayment\Model\ResourceModel\MasterPaymentMatrixAdjustment\Collection;
use Trans\MasterPayment\Model\ResourceModel\MasterPaymentMatrixAdjustment\CollectionFactory as CollectionFactory;

/**
 * Class MasterPaymentRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MasterPaymentMatrixAdjustmentRepository implements MasterPaymentMatrixAdjustmentRepositoryInterface {
	/**
	 * @var array
	 */
	protected $instances = [];

	/**
	 * @var Resource
	 */
	protected $resource;

	/**
	 * @var CollectionFactory
	 */
	protected $collectionFactory;

	/**
	 * @var MasterPaymentResponseSearchResultsInterfaceFactory
	 */
	protected $searchResultsFactory;

	/**
	 * @var MasterPaymentMatrixAdjustmentInterfaceFactory
	 */
	protected $interfaceFactory;

	/**
	 * @var DataObjectHelper
	 */
	protected $dataObjectHelper;

	/**
	 * @var StoreManager
	 */
	protected $storeManager;

	/**
	 * @param Resource $resource
	 * @param CollectionFactory $collectionFactory
	 * @param MasterPaymentMatrixAdjustmentSearchResultsInterfaceFactory $searchResultsFactory
	 * @param MasterPaymentMatrixAdjustmentInterfaceFactory $interfaceFactory
	 * @param DataObjectHelper $dataObjectHelper
	 * @param StoreManagerInterface $storeManager
	 */
	public function __construct(
		Resource $resource,
		CollectionFactory $collectionFactory,
		MasterPaymentMatrixAdjustmentSearchResultsInterfaceFactory $searchResultsFactory,
		MasterPaymentMatrixAdjustmentInterfaceFactory $interfaceFactory,
		DataObjectHelper $dataObjectHelper,
		StoreManagerInterface $storeManager
	) {
		$this->resource             = $resource;
		$this->collectionFactory    = $collectionFactory;
		$this->searchResultsFactory = $searchResultsFactory;
		$this->interfaceFactory     = $interfaceFactory;
		$this->dataObjectHelper     = $dataObjectHelper;
		$this->storeManager         = $storeManager;
	}

	/**
	 * Save page.
	 *
	 * @param \Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterface $data
	 * @return \Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(MasterPaymentMatrixAdjustmentInterface $data) {
		/** @var MasterPaymentResInterface|\Magento\Framework\Model\AbstractModel $data */

		try {
			$this->resource->save($data);
		} catch (\Exception $exception) {
			throw new CouldNotSaveException(__(
				'Could not save the Payment Flag: %1',
				$exception->getMessage()
			));
		}
		return $data;
	}

	/**
	 * Retrieve MasterPaymentResponse.
	 *
	 * @param int $id
	 * @return \Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($id) {
		if (!isset($this->instances[$id])) {
			/** @var \Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterface|\Magento\Framework\Model\AbstractModel $data */
			$data = $this->interfaceFactory->create();
			$this->resource->load($data, $id);
			if (!$data->getId()) {
				throw new NoSuchEntityException(__('Requested Payment Flag doesn\'t exist'));
			}
			$this->instances[$id] = $data;
		}
		return $this->instances[$id];
	}

	/**
	 * Retrieve MasterPayment Response By transaction no.
	 *
	 * @param int $transNo
	 * @return \Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getByTransactionNo($transNo) {
		if (!isset($this->instances[$transNo])) {
			/** @var /** @var \Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterface|\Magento\Framework\Model\AbstractModel $data */
			$data = $this->interfaceFactory->create();
			$this->resource->load($data, $transNo, 'transaction_no');
			if (!$data->getId()) {
				throw new NoSuchEntityException(__('Requested Transaction Number doesn\'t exist'));
			}

			$this->instances[$transNo] = $data;
		}

		return $this->instances[$transNo];
	}

	/**
	 * Retrieve pages matching the specified criteria.
	 *
	 * @param SearchCriteriaInterface $searchCriteria
	 * @return \Trans\MasterPayment\Api\Data\MasterPaymentSearchResultsInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 * @SuppressWarnings(PHPMD)
	 */
	public function getList(SearchCriteriaInterface $searchCriteria) {
		/** @var \Trans\MasterPayment\Api\Data\MasterPaymentSearchResultsInterface $searchResults */
		$searchResults = $this->searchResultsFactory->create();
		$searchResults->setSearchCriteria($searchCriteria);

		/** @var \Trans\MasterPayment\Model\ResourceModel\MasterPayment\Collection $collection */
		$collection = $this->collectionFactory->create();

		//Add filters from root filter group to the collection
		/** @var FilterGroup $group */
		foreach ($searchCriteria->getFilterGroups() as $group) {
			$this->addFilterGroupToCollection($group, $collection);
		}
		$sortOrders = $searchCriteria->getSortOrders();
		/** @var SortOrder $sortOrder */
		if ($sortOrders) {
			foreach ($searchCriteria->getSortOrders() as $sortOrder) {
				$field = $sortOrder->getField();
				$collection->addOrder(
					$field,
					($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
				);
			}
		} else {
			// set a default sorting order since this method is used constantly in many
			// different blocks
			$field = 'id';
			$collection->addOrder($field, 'ASC');
		}

		$collection->setCurPage($searchCriteria->getCurrentPage());
		$collection->setPageSize($searchCriteria->getPageSize());

		/** @var \Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterface[] $data */
		$datas = [];
		/** @var \Trans\MasterPayment\Model\MasterPayment $data */
		foreach ($collection as $data) {
			/** @var \Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterface $dataDataObject */
			$dataDataObject = $this->interfaceFactory->create();
			$this->dataObjectHelper->populateWithArray($dataDataObject, $data->getData(), MasterPaymentMatrixAdjustmentInterface::class);
			$datas[] = $dataDataObject;
		}

		$searchResults->setTotalCount($collection->getSize());
		return $searchResults->setItems($datas);
	}

	/**
	 * Delete MasterPayment Response.
	 *
	 * @param \Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterface $data
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(MasterPaymentMatrixAdjustmentInterface $data) {
		/** @var \Trans\MasterPayment\Api\Data\MasterPaymentMatrixAdjustmentInterface|\Magento\Framework\Model\AbstractModel $data */
		$id = $data->getId();
		try {
			unset($this->instances[$id]);
			$this->resource->delete($data);
		} catch (ValidatorException $e) {
			throw new CouldNotSaveException(__($e->getMessage()));
		} catch (\Exception $e) {
			throw new StateException(
				__('Unable to remove MasterPayment Response %1', $id)
			);
		}
		unset($this->instances[$id]);
		return true;
	}

	/**
	 * Delete MasterPayment Response by ID.
	 *
	 * @param int $id
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($id) {
		$data = $this->getById($id);
		return $this->delete($data);
	}

	/**
	 * Helper function that adds a FilterGroup to the collection.
	 *
	 * @param FilterGroup $filterGroup
	 * @param Collection $collection
	 * @return $this
	 * @throws \Magento\Framework\Exception\InputException
	 */
	protected function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $collection) {
		$fields     = [];
		$conditions = [];
		foreach ($filterGroup->getFilters() as $filter) {
			$condition    = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
			$fields[]     = $filter->getField();
			$conditions[] = [$condition => $filter->getValue()];
		}
		if ($fields) {
			$collection->addFieldToFilter($fields, $conditions);
		}
		return $this;
	}
}
