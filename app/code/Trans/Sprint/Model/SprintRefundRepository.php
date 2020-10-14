<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
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
use Trans\Sprint\Api\Data\SprintRefundInterface;
use Trans\Sprint\Api\Data\SprintRefundInterfaceFactory;
use Trans\Sprint\Api\Data\SprintRefundSearchResultsInterfaceFactory;
use Trans\Sprint\Api\SprintRefundRepositoryInterface;
use Trans\Sprint\Model\ResourceModel\SprintRefund as ResourceSprintRefund;
use Trans\Sprint\Model\ResourceModel\SprintRefund\Collection;
use Trans\Sprint\Model\ResourceModel\SprintRefund\CollectionFactory as CollectionFactory;

/**
 * Class SprintRefundRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SprintRefundRepository implements SprintRefundRepositoryInterface {
	/**
	 * @var array
	 */
	protected $instances = [];

	/**
	 * @var ResourceSprintRefund
	 */
	protected $resource;

	/**
	 * @var CollectionFactory
	 */
	protected $collectionFactory;

	/**
	 * @var SprintResponseSearchResultsInterfaceFactory
	 */
	protected $searchResultsFactory;

	/**
	 * @var SprintRefundInterfaceFactory
	 */
	protected $refundInterface;

	/**
	 * @var DataObjectHelper
	 */
	protected $dataObjectHelper;

	/**
	 * @var StoreManager
	 */
	protected $storeManager;

	/**
	 * @param ResourceSprintRefund $resource
	 * @param CollectionFactory $collectionFactory
	 * @param SprintRefundSearchResultsInterfaceFactory $searchResultsFactory
	 * @param SprintRefundInterfaceFactory $refundInterface
	 * @param DataObjectHelper $dataObjectHelper
	 * @param StoreManagerInterface $storeManager
	 */
	public function __construct(
		ResourceSprintRefund $resource,
		CollectionFactory $collectionFactory,
		SprintRefundSearchResultsInterfaceFactory $searchResultsFactory,
		SprintRefundInterfaceFactory $refundInterface,
		DataObjectHelper $dataObjectHelper,
		StoreManagerInterface $storeManager
	) {
		$this->resource             = $resource;
		$this->collectionFactory    = $collectionFactory;
		$this->searchResultsFactory = $searchResultsFactory;
		$this->refundInterface      = $refundInterface;
		$this->dataObjectHelper     = $dataObjectHelper;
		$this->storeManager         = $storeManager;
	}

	/**
	 * Save page.
	 *
	 * @param \Trans\Sprint\Api\Data\SprintRefundInterface $sprintRefund
	 * @return \Trans\Sprint\Api\Data\SprintRefundInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(SprintRefundInterface $sprintRefund) {
		/** @var sprintResInterface|\Magento\Framework\Model\AbstractModel $sprintRefund */

		try {
			$this->resource->save($sprintRefund);
		} catch (\Exception $exception) {
			throw new CouldNotSaveException(__(
				'Could not save the Payment Flag: %1',
				$exception->getMessage()
			));
		}
		return $sprintRefund;
	}

	/**
	 * Retrieve SprintResponse.
	 *
	 * @param int $sprintRefundId
	 * @return \Trans\Sprint\Api\Data\SprintRefundInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($sprintRefundId) {
		if (!isset($this->instances[$sprintRefundId])) {
			/** @var \Trans\Sprint\Api\Data\SprintRefundInterface|\Magento\Framework\Model\AbstractModel $sprintRefund */
			$sprintRefund = $this->refundInterface->create();
			$this->resource->load($sprintRefund, $sprintRefundId);
			if (!$sprintRefund->getId()) {
				throw new NoSuchEntityException(__('Requested Payment Flag doesn\'t exist'));
			}
			$this->instances[$sprintRefundId] = $sprintRefund;
		}
		return $this->instances[$sprintRefundId];
	}

	/**
	 * Retrieve Sprint Response By transaction no.
	 *
	 * @param int $transNo
	 * @return \Trans\Sprint\Api\Data\SprintRefundInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getByTransactionNo($transNo) {
		if (!isset($this->instances[$transNo])) {
			/** @var /** @var \Trans\Sprint\Api\Data\SprintRefundInterface|\Magento\Framework\Model\AbstractModel $sprintRefund */
			$sprintRefund = $this->refundInterface->create();
			$this->resource->load($sprintRefund, $transNo, 'transaction_no');
			if (!$sprintRefund->getId()) {
				throw new NoSuchEntityException(__('Requested Payment Flag doesn\'t exist'));
			}

			$this->instances[$transNo] = $sprintRefund;
		}

		return $this->instances[$transNo];
	}

	/**
	 * Retrieve pages matching the specified criteria.
	 *
	 * @param SearchCriteriaInterface $searchCriteria
	 * @return \Trans\Sprint\Api\Data\SprintRefundSearchResultsInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 * @SuppressWarnings(PHPMD)
	 */
	public function getList(SearchCriteriaInterface $searchCriteria) {
		/** @var \Trans\Sprint\Api\Data\SprintRefundSearchResultsInterface $searchResults */
		$searchResults = $this->searchResultsFactory->create();
		$searchResults->setSearchCriteria($searchCriteria);

		/** @var \Trans\Sprint\Model\ResourceModel\SprintRefund\Collection $collection */
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

		/** @var \Trans\Sprint\Api\Data\SprintRefundInterface[] $sprintRefund */
		$sprintRefunds = [];
		/** @var \Trans\Sprint\Model\SprintRefund $sprintRefund */
		foreach ($collection as $sprintRefund) {
			/** @var \Trans\Sprint\Api\Data\SprintRefundInterface $sprintRefundDataObject */
			$sprintRefundDataObject = $this->refundInterface->create();
			$this->dataObjectHelper->populateWithArray($sprintRefundDataObject, $sprintRefund->getData(), SprintRefundInterface::class);
			$sprintRefunds[] = $sprintRefundDataObject;
		}

		$searchResults->setTotalCount($collection->getSize());
		return $searchResults->setItems($sprintRefunds);
	}

	/**
	 * Delete Sprint Response.
	 *
	 * @param \Trans\Sprint\Api\Data\SprintRefundInterface $sprintRefund
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(SprintRefundInterface $sprintRefund) {
		/** @var \Trans\Sprint\Api\Data\SprintRefundInterface|\Magento\Framework\Model\AbstractModel $sprintRefund */
		$sprintRefundId = $sprintRefund->getId();
		try {
			unset($this->instances[$sprintRefundId]);
			$this->resource->delete($sprintRefund);
		} catch (ValidatorException $e) {
			throw new CouldNotSaveException(__($e->getMessage()));
		} catch (\Exception $e) {
			throw new StateException(
				__('Unable to remove Sprint Response %1', $sprintRefundId)
			);
		}
		unset($this->instances[$sprintRefundId]);
		return true;
	}

	/**
	 * Delete Sprint Response by ID.
	 *
	 * @param int $sprintRefundId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($sprintRefundId) {
		$sprintRefund = $this->getById($sprintRefundId);
		return $this->delete($sprintRefund);
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
