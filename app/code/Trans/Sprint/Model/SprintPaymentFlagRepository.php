<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Store\Model\StoreManagerInterface;
use Trans\Sprint\Api\Data\SprintPaymentFlagInterface;
use Trans\Sprint\Api\Data\SprintPaymentFlagInterfaceFactory;
use Trans\Sprint\Api\Data\SprintPaymentFlagSearchResultsInterfaceFactory;
use Trans\Sprint\Api\SprintPaymentFlagRepositoryInterface;
use Trans\Sprint\Model\ResourceModel\SprintPaymentFlag as ResourceSprintPaymentFlag;
use Trans\Sprint\Model\ResourceModel\SprintPaymentFlag\Collection;
use Trans\Sprint\Model\ResourceModel\SprintPaymentFlag\CollectionFactory as SprintPaymentFlagCollectionFactory;

/**
 * Class SprintPaymentFlagRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SprintPaymentFlagRepository implements SprintPaymentFlagRepositoryInterface {
	/**
	 * @var array
	 */
	protected $instances = [];

	/**
	 * @var ResourceSprintPaymentFlag
	 */
	protected $resource;

	/**
	 * @var SprintPaymentFlagCollectionFactory
	 */
	protected $collection;

	/**
	 * @var SprintResponseSearchResultsInterfaceFactory
	 */
	protected $searchResultsFactory;

	/**
	 * @var SprintPaymentFlagInterfaceFactory
	 */
	protected $paymentFlagInterface;

	/**
	 * @var DataObjectHelper
	 */
	protected $dataObjectHelper;

	/**
	 * @var StoreManager
	 */
	protected $storeManager;

	/**
	 * @param ResourceSprintPaymentFlag $resource
	 * @param SprintPaymentFlagCollectionFactory $collection
	 * @param SprintPaymentFlagSearchResultsInterfaceFactory $searchResultsFactory
	 * @param SprintPaymentFlagInterfaceFactory $paymentFlagInterface
	 * @param DataObjectHelper $dataObjectHelper
	 * @param StoreManagerInterface $storeManager
	 */
	public function __construct(
		ResourceSprintPaymentFlag $resource,
		SprintPaymentFlagCollectionFactory $collection,
		SprintPaymentFlagSearchResultsInterfaceFactory $searchResultsFactory,
		SprintPaymentFlagInterfaceFactory $paymentFlagInterface,
		DataObjectHelper $dataObjectHelper,
		StoreManagerInterface $storeManager
	) {
		$this->resource             = $resource;
		$this->collection           = $collection;
		$this->searchResultsFactory = $searchResultsFactory;
		$this->paymentFlagInterface = $paymentFlagInterface;
		$this->dataObjectHelper     = $dataObjectHelper;
		$this->storeManager         = $storeManager;
	}

	/**
	 * Save page.
	 *
	 * @param \Trans\Sprint\Api\Data\SprintPaymentFlagInterface $sprintPaymentFlag
	 * @return \Trans\Sprint\Api\Data\SprintPaymentFlagInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(SprintPaymentFlagInterface $sprintPaymentFlag) {
		/** @var sprintResInterface|\Magento\Framework\Model\AbstractModel $sprintPaymentFlag */

		try {
			$this->resource->save($sprintPaymentFlag);
		} catch (\Exception $exception) {
			throw new CouldNotSaveException(__(
				'Could not save the Payment Flag: %1',
				$exception->getMessage()
			));
		}
		return $sprintPaymentFlag;
	}

	/**
	 * Retrieve SprintResponse.
	 *
	 * @param int $sprintPaymentFlagId
	 * @return \Trans\Sprint\Api\Data\SprintPaymentFlagInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($sprintPaymentFlagId) {
		if (!isset($this->instances[$sprintPaymentFlagId])) {
			/** @var \Trans\Sprint\Api\Data\SprintPaymentFlagInterface|\Magento\Framework\Model\AbstractModel $sprintPaymentFlag */
			$sprintPaymentFlag = $this->paymentFlagInterface->create();
			$this->resource->load($sprintPaymentFlag, $sprintPaymentFlagId);
			if (!$sprintPaymentFlag->getId()) {
				throw new NoSuchEntityException(__('Requested Payment Flag doesn\'t exist'));
			}
			$this->instances[$sprintPaymentFlagId] = $sprintPaymentFlag;
		}
		return $this->instances[$sprintPaymentFlagId];
	}

	/**
	 * Retrieve Sprint Response By transaction no.
	 *
	 * @param int $transNo
	 * @return \Trans\Sprint\Api\Data\SprintPaymentFlagInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getByTransactionNo($transNo) {
		if (!isset($this->instances[$transNo])) {
			/** @var /** @var \Trans\Sprint\Api\Data\SprintPaymentFlagInterface|\Magento\Framework\Model\AbstractModel $sprintPaymentFlag */
			$sprintPaymentFlag = $this->paymentFlagInterface->create();
			$this->resource->load($sprintPaymentFlag, $transNo, 'transaction_no');
			if (!$sprintPaymentFlag->getId()) {
				throw new NoSuchEntityException(__('Requested Payment Flag doesn\'t exist'));
			}

			$this->instances[$transNo] = $sprintPaymentFlag;
		}

		return $this->instances[$transNo];
	}

	/**
	 * Retrieve pages matching the specified criteria.
	 *
	 * @param SearchCriteriaInterface $searchCriteria
	 * @return \Trans\Sprint\Api\Data\SprintPaymentFlagSearchResultsInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 * @SuppressWarnings(PHPMD)
	 */
	public function getList(SearchCriteriaInterface $searchCriteria) {
		/** @var \Trans\Sprint\Api\Data\SprintPaymentFlagSearchResultsInterface $searchResults */
		$searchResults = $this->searchResultsFactory->create();
		$searchResults->setSearchCriteria($searchCriteria);

		/** @var \Trans\Sprint\Model\ResourceModel\SprintPaymentFlag\Collection $collection */
		$collection = $this->collection->create();

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

		/** @var \Trans\Sprint\Api\Data\SprintPaymentFlagInterface[] $sprintPaymentFlag */
		$sprintPaymentFlags = [];
		/** @var \Trans\Sprint\Model\SprintPaymentFlag $sprintPaymentFlag */
		foreach ($collection as $sprintPaymentFlag) {
			/** @var \Trans\Sprint\Api\Data\SprintPaymentFlagInterface $sprintPaymentFlagDataObject */
			$sprintPaymentFlagDataObject = $this->paymentFlagInterface->create();
			$this->dataObjectHelper->populateWithArray($sprintPaymentFlagDataObject, $sprintPaymentFlag->getData(), SprintPaymentFlagInterface::class);
			$sprintPaymentFlags[] = $sprintPaymentFlagDataObject;
		}

		$searchResults->setTotalCount($collection->getSize());
		return $searchResults->setItems($sprintPaymentFlags);
	}

	/**
	 * Delete Sprint Response.
	 *
	 * @param \Trans\Sprint\Api\Data\SprintPaymentFlagInterface $sprintPaymentFlag
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(SprintPaymentFlagInterface $sprintPaymentFlag) {
		/** @var \Trans\Sprint\Api\Data\SprintPaymentFlagInterface|\Magento\Framework\Model\AbstractModel $sprintPaymentFlag */
		$sprintPaymentFlagId = $sprintPaymentFlag->getId();
		try {
			unset($this->instances[$sprintPaymentFlagId]);
			$this->resource->delete($sprintPaymentFlag);
		} catch (ValidatorException $e) {
			throw new CouldNotSaveException(__($e->getMessage()));
		} catch (\Exception $e) {
			throw new StateException(
				__('Unable to remove Sprint Response %1', $sprintPaymentFlagId)
			);
		}
		unset($this->instances[$sprintPaymentFlagId]);
		return true;
	}

	/**
	 * Delete Sprint Response by ID.
	 *
	 * @param int $sprintPaymentFlagId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($sprintPaymentFlagId) {
		$sprintPaymentFlag = $this->getById($sprintPaymentFlagId);
		return $this->delete($sprintPaymentFlag);
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
