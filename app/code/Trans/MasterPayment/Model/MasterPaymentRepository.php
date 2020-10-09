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
use Trans\MasterPayment\Api\Data\MasterPaymentInterface;
use Trans\MasterPayment\Api\Data\MasterPaymentInterfaceFactory;
use Trans\MasterPayment\Api\Data\MasterPaymentSearchResultsInterfaceFactory;
use Trans\MasterPayment\Api\MasterPaymentRepositoryInterface;
use Trans\MasterPayment\Model\ResourceModel\MasterPayment as ResourceMasterPayment;
use Trans\MasterPayment\Model\ResourceModel\MasterPayment\Collection;
use Trans\MasterPayment\Model\ResourceModel\MasterPayment\CollectionFactory as CollectionFactory;

/**
 * Class MasterPaymentRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MasterPaymentRepository implements MasterPaymentRepositoryInterface {
	/**
	 * @var array
	 */
	protected $instances = [];

	/**
	 * @var ResourceMasterPayment
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
	 * @var MasterPaymentInterfaceFactory
	 */
	protected $masterPaymentInterface;

	/**
	 * @var DataObjectHelper
	 */
	protected $dataObjectHelper;

	/**
	 * @var StoreManager
	 */
	protected $storeManager;

	/**
	 * @param ResourceMasterPayment $resource
	 * @param CollectionFactory $collectionFactory
	 * @param MasterPaymentSearchResultsInterfaceFactory $searchResultsFactory
	 * @param MasterPaymentInterfaceFactory $masterPaymentInterface
	 * @param DataObjectHelper $dataObjectHelper
	 * @param StoreManagerInterface $storeManager
	 */
	public function __construct(
		ResourceMasterPayment $resource,
		CollectionFactory $collectionFactory,
		MasterPaymentSearchResultsInterfaceFactory $searchResultsFactory,
		MasterPaymentInterfaceFactory $masterPaymentInterface,
		DataObjectHelper $dataObjectHelper,
		StoreManagerInterface $storeManager
	) {
		$this->resource               = $resource;
		$this->collectionFactory      = $collectionFactory;
		$this->searchResultsFactory   = $searchResultsFactory;
		$this->masterPaymentInterface = $masterPaymentInterface;
		$this->dataObjectHelper       = $dataObjectHelper;
		$this->storeManager           = $storeManager;
	}

	/**
	 * Save page.
	 *
	 * @param \Trans\MasterPayment\Api\Data\MasterPaymentInterface $masterPayment
	 * @return \Trans\MasterPayment\Api\Data\MasterPaymentInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(MasterPaymentInterface $masterPayment) {
		/** @var MasterPaymentResInterface|\Magento\Framework\Model\AbstractModel $masterPayment */

		try {
			$this->resource->save($masterPayment);
		} catch (\Exception $exception) {
			throw new CouldNotSaveException(__(
				'Could not save the Payment Flag: %1',
				$exception->getMessage()
			));
		}
		return $masterPayment;
	}

	/**
	 * Retrieve MasterPaymentResponse.
	 *
	 * @param int $masterPaymentId
	 * @return \Trans\MasterPayment\Api\Data\MasterPaymentInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($masterPaymentId) {
		if (!isset($this->instances[$masterPaymentId])) {
			/** @var \Trans\MasterPayment\Api\Data\MasterPaymentInterface|\Magento\Framework\Model\AbstractModel $masterPayment */
			$masterPayment = $this->masterPaymentInterface->create();
			$this->resource->load($masterPayment, $masterPaymentId);
			if (!$masterPayment->getId()) {
				throw new NoSuchEntityException(__('Requested Payment Flag doesn\'t exist'));
			}
			$this->instances[$masterPaymentId] = $masterPayment;
		}
		return $this->instances[$masterPaymentId];
	}

	/**
	 * Retrieve MasterPayment Response By transaction no.
	 *
	 * @param int $transNo
	 * @return \Trans\MasterPayment\Api\Data\MasterPaymentInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getByTransactionNo($transNo) {
		if (!isset($this->instances[$transNo])) {
			/** @var /** @var \Trans\MasterPayment\Api\Data\MasterPaymentInterface|\Magento\Framework\Model\AbstractModel $masterPayment */
			$masterPayment = $this->masterPaymentInterface->create();
			$this->resource->load($masterPayment, $transNo, 'transaction_no');
			if (!$masterPayment->getId()) {
				throw new NoSuchEntityException(__('Requested Payment Flag doesn\'t exist'));
			}

			$this->instances[$transNo] = $masterPayment;
		}

		return $this->instances[$transNo];
	}

	/**
	 * Get Payment Id by
	 *
	 * @param string $paymentMethod
	 * @param int $terms
	 * @return array
	 */
	public function getPaymentId($paymentMethod, $terms = null) {
		$collection = $this->collectionFactory->create();
		$collection->addFieldToFilter(MasterPaymentInterface::PAYMENT_METHOD, $paymentMethod);
		if ($terms) {
			$collection->addFieldToFilter(MasterPaymentInterface::PAYMENT_TERMS, $terms);
		}

		return $collection->getFirstItem();
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

		/** @var \Trans\MasterPayment\Api\Data\MasterPaymentInterface[] $masterPayment */
		$masterPayments = [];
		/** @var \Trans\MasterPayment\Model\MasterPayment $masterPayment */
		foreach ($collection as $masterPayment) {
			/** @var \Trans\MasterPayment\Api\Data\MasterPaymentInterface $masterPaymentDataObject */
			$masterPaymentDataObject = $this->masterPaymentInterface->create();
			$this->dataObjectHelper->populateWithArray($masterPaymentDataObject, $masterPayment->getData(), MasterPaymentInterface::class);
			$masterPayments[] = $masterPaymentDataObject;
		}

		$searchResults->setTotalCount($collection->getSize());
		return $searchResults->setItems($masterPayments);
	}

	/**
	 * Delete MasterPayment Response.
	 *
	 * @param \Trans\MasterPayment\Api\Data\MasterPaymentInterface $masterPayment
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(MasterPaymentInterface $masterPayment) {
		/** @var \Trans\MasterPayment\Api\Data\MasterPaymentInterface|\Magento\Framework\Model\AbstractModel $masterPayment */
		$masterPaymentId = $masterPayment->getId();
		try {
			unset($this->instances[$masterPaymentId]);
			$this->resource->delete($masterPayment);
		} catch (ValidatorException $e) {
			throw new CouldNotSaveException(__($e->getMessage()));
		} catch (\Exception $e) {
			throw new StateException(
				__('Unable to remove MasterPayment Response %1', $masterPaymentId)
			);
		}
		unset($this->instances[$masterPaymentId]);
		return true;
	}

	/**
	 * Delete MasterPayment Response by ID.
	 *
	 * @param int $masterPaymentId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($masterPaymentId) {
		$masterPayment = $this->getById($masterPaymentId);
		return $this->delete($masterPayment);
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
