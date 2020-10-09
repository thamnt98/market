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
use Trans\Sprint\Api\Data\SprintResponseInterface;
use Trans\Sprint\Api\Data\SprintResponseInterfaceFactory;
use Trans\Sprint\Api\Data\SprintResponseSearchResultsInterfaceFactory;
use Trans\Sprint\Api\SprintResponseRepositoryInterface;
use Trans\Sprint\Model\ResourceModel\SprintResponse as ResourceSprintResponse;
use Trans\Sprint\Model\ResourceModel\SprintResponse\Collection;
use Trans\Sprint\Model\ResourceModel\SprintResponse\CollectionFactory as SprintResCollectionFactory;

/**
 * Class SprintResponseRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SprintResponseRepository implements SprintResponseRepositoryInterface {
	/**
	 * @var array
	 */
	protected $instances = [];

	/**
	 * @var ResourceSprintResponse
	 */
	protected $resource;

	/**
	 * @var sprintResCollectionFactory
	 */
	protected $sprintResCollection;

	/**
	 * @var Collection
	 */
	protected $responseCollection;

	/**
	 * @var SprintResponseSearchResultsInterfaceFactory
	 */
	protected $searchResultsFactory;

	/**
	 * @var SprintResInterfaceFactory
	 */
	protected $sprintResInterface;

	/**
	 * @var DataObjectHelper
	 */
	protected $dataObjectHelper;

	/**
	 * @var StoreManager
	 */
	protected $storeManager;

	/**
	 * @param ResourceSprintResponse $resource
	 * @param sprintResCollectionFactory $sprintResCollection
	 * @param Collection $responseCollection
	 * @param SprintResponseSearchResultsInterfaceFactory $searchResultsFactory
	 * @param SprintResponseInterfaceFactory $sprintResInterface
	 * @param DataObjectHelper $dataObjectHelper
	 * @param StoreManagerInterface $storeManager
	 */
	public function __construct(
		ResourceSprintResponse $resource,
		Collection $responseCollection,
		SprintResCollectionFactory $sprintResCollection,
		SprintResponseSearchResultsInterfaceFactory $searchResultsFactory,
		SprintResponseInterfaceFactory $sprintResInterface,
		DataObjectHelper $dataObjectHelper,
		StoreManagerInterface $storeManager
	) {
		$this->resource             = $resource;
		$this->responseCollection   = $responseCollection;
		$this->sprintResCollection  = $sprintResCollection;
		$this->searchResultsFactory = $searchResultsFactory;
		$this->sprintResInterface   = $sprintResInterface;
		$this->dataObjectHelper     = $dataObjectHelper;
		$this->storeManager         = $storeManager;
	}

	/**
	 * Save page.
	 *
	 * @param \Trans\Sprint\Api\Data\SprintResponseInterface $sprintResponse
	 * @return \Trans\Sprint\Api\Data\SprintResponseInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(SprintResponseInterface $sprintResponse) {
		/** @var sprintResInterface|\Magento\Framework\Model\AbstractModel $sprintResponse */

		try {
			$this->resource->save($sprintResponse);
		} catch (\Exception $exception) {
			throw new CouldNotSaveException(__(
				'Could not save the Sprint Response: %1',
				$exception->getMessage()
			));
		}
		return $sprintResponse;
	}

	/**
	 * Retrieve SprintResponse.
	 *
	 * @param int $sprintResponseId
	 * @return \Trans\Sprint\Api\Data\SprintResponseInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($sprintResponseId) {
		if (!isset($this->instances[$sprintResponseId])) {
			/** @var \Trans\Sprint\Api\Data\SprintResponseInterface|\Magento\Framework\Model\AbstractModel $sprintResponse */
			$sprintResponse = $this->sprintResInterface->create();
			$this->resource->load($sprintResponse, $sprintResponseId);
			if (!$sprintResponse->getId()) {
				throw new NoSuchEntityException(__('Requested Sprint Response doesn\'t exist'));
			}
			$this->instances[$sprintResponseId] = $sprintResponse;
		}
		return $this->instances[$sprintResponseId];
	}

	/**
	 * Retrieve Sprint Response By quote id.
	 *
	 * @param int $quoteId
	 * @param int $storeId
	 * @return \Trans\Sprint\Api\Data\SprintResponseInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getByQuoteId($quoteId, $storeId = null) {
		if (!isset($this->instances[$quoteId])) {
			if (!$storeId) {
				$storeId = $this->storeManager->getStore()->getId();
			}
			/** @var \Trans\Sprint\Model\ResourceModel\SprintResponse\CollectionFactory|\Magento\Framework\Model\AbstractModel $customOrderItem */
			$data = $this->sprintResCollection->create($quoteId, null, $storeId)->addFieldToSelect('*')->setOrder('id', 'ASC')->setPageSize(1)->load()->getFirstItem();

			if (count($data) === 0) {
				throw new NoSuchEntityException(__('Requested Item doesn\'t exist'));
			}

			$this->instances[$quoteId] = $data;
		}

		return $this->instances[$quoteId];
	}

	/**
	 * Retrieve Sprint Response By transaction no.
	 *
	 * @param int $transNo
	 * @return \Trans\Sprint\Api\Data\SprintResponseInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getByTransactionNo($transNo, $storeId = null) {
		if (!isset($this->instances[$transNo])) {
			if (!$storeId) {
				$storeId = $this->storeManager->getStore()->getId();
			}
			/** @var \Trans\Sprint\Api\Data\SprintResponseInterface|\Magento\Framework\Model\AbstractModel $sprintResponse */
			$sprintResponse = $this->sprintResInterface->create();
			$this->resource->load($sprintResponse, $transNo, 'transaction_no');
			
			if (!$sprintResponse->getId()) {
				return $this->sprintResInterface->create();
				// throw new NoSuchEntityException(__('Requested data doesn\'t exist'));
			}

			$this->instances[$transNo] = $sprintResponse;
		}

		return $this->instances[$transNo];
	}

	/**
	 * Retrieve pages matching the specified criteria.
	 *
	 * @param SearchCriteriaInterface $searchCriteria
	 * @return \Trans\Sprint\Api\Data\SprintResponseSearchResultsInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 * @SuppressWarnings(PHPMD)
	 */
	public function getList(SearchCriteriaInterface $searchCriteria) {
		/** @var \Trans\Sprint\Api\Data\SprintResponseSearchResultsInterface $searchResults */
		$searchResults = $this->searchResultsFactory->create();
		$searchResults->setSearchCriteria($searchCriteria);

		/** @var \Trans\Sprint\Model\ResourceModel\SprintResponse\Collection $collection */
		$collection = $this->sprintResCollection->create();

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

		/** @var \Trans\Sprint\Api\Data\SprintResponseInterface[] $sprintResponse */
		$sprintResponses = [];
		/** @var \Trans\Sprint\Model\SprintResponse $sprintResponse */
		foreach ($collection as $sprintResponse) {
			/** @var \Trans\Sprint\Api\Data\SprintResponseInterface $sprintResponseDataObject */
			$sprintResponseDataObject = $this->sprintResInterface->create();
			$this->dataObjectHelper->populateWithArray($sprintResponseDataObject, $sprintResponse->getData(), SprintResponseInterface::class);
			$sprintResponses[] = $sprintResponseDataObject;
		}

		$searchResults->setTotalCount($collection->getSize());
		return $searchResults->setItems($sprintResponses);
	}

	/**
	 * Delete Sprint Response.
	 *
	 * @param \Trans\Sprint\Api\Data\SprintResponseInterface $sprintResponse
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(SprintResponseInterface $sprintResponse) {
		/** @var \Trans\Sprint\Api\Data\SprintResponseInterface|\Magento\Framework\Model\AbstractModel $sprintResponse */
		$sprintResponseId = $sprintResponse->getId();
		try {
			unset($this->instances[$sprintResponseId]);
			$this->resource->delete($sprintResponse);
		} catch (ValidatorException $e) {
			throw new CouldNotSaveException(__($e->getMessage()));
		} catch (\Exception $e) {
			throw new StateException(
				__('Unable to remove Sprint Response %1', $sprintResponseId)
			);
		}
		unset($this->instances[$sprintResponseId]);
		return true;
	}

	/**
	 * Delete Sprint Response by ID.
	 *
	 * @param int $sprintResponseId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($sprintResponseId) {
		$sprintResponse = $this->getById($sprintResponseId);
		return $this->delete($sprintResponse);
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
