<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@transdigital.co.id>
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
use Trans\Sprint\Api\Data\BankBinInterface;
use Trans\Sprint\Api\Data\BankBinInterfaceFactory;
use Trans\Sprint\Api\Data\BankBinSearchResultsInterfaceFactory;
use Trans\Sprint\Api\BankBinRepositoryInterface;
use Trans\Sprint\Model\ResourceModel\BankBin as ResourceBank;
use Trans\Sprint\Model\ResourceModel\BankBin\Collection;
use Trans\Sprint\Model\ResourceModel\BankBin\CollectionFactory as collectionFactory;

/**
 * Class BankRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BankBinRepository implements BankBinRepositoryInterface {
	/**
	 * @var array
	 */
	protected $instances = [];

	/**
	 * @var ResourceBank
	 */
	protected $resource;

	/**
	 * @var CollectionFactory
	 */
	protected $collection;

	/**
	 * @var BankBinSearchResultsInterfaceFactory
	 */
	protected $searchResultsFactory;

	/**
	 * @var BankBinInterfaceFactory
	 */
	protected $bankBinInterface;

	/**
	 * @var DataObjectHelper
	 */
	protected $dataObjectHelper;

	/**
	 * @var StoreManager
	 */
	protected $storeManager;

	/**
	 * @param ResourceBank $resource
	 * @param CollectionFactory $collection

	 * @param BankBinSearchResultsInterfaceFactory $searchResultsFactory
	 * @param BankBinInterfaceFactory $bankBinInterface
	 * @param DataObjectHelper $dataObjectHelper
	 * @param StoreManagerInterface $storeManager
	 */
	public function __construct(
		ResourceBank $resource,
		CollectionFactory $collection,
		BankBinSearchResultsInterfaceFactory $searchResultsFactory,
		BankBinInterfaceFactory $bankBinInterface,
		DataObjectHelper $dataObjectHelper,
		StoreManagerInterface $storeManager
	) {
		$this->resource             = $resource;
		$this->collection  			= $collection;
		$this->searchResultsFactory = $searchResultsFactory;
		$this->bankBinInterface   		= $bankBinInterface;
		$this->dataObjectHelper     = $dataObjectHelper;
		$this->storeManager         = $storeManager;
	}

	/**
	 * Save page.
	 *
	 * @param \Trans\Sprint\Api\Data\BankBinInterface $bank
	 * @return \Trans\Sprint\Api\Data\BankBinInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(BankBinInterface $bankBin) {
		/** @var BankBinInterface|\Magento\Framework\Model\AbstractModel $bank */

		try {
			$this->resource->save($bankBin);
		} catch (\Exception $exception) {
			throw new CouldNotSaveException(__(
				'Could not save the Bank Bin Data: %1',
				$exception->getMessage()
			));
		}
		return $bankBin;
	}

	/**
	 * Retrieve bank.
	 *
	 * @param int $bankBinId
	 * @return \Trans\Sprint\Api\Data\BankBinInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($bankBinId) {
		if (!isset($this->instances[$bankBinId])) {
			/** @var \Trans\Sprint\Api\Data\BankBinInterface|\Magento\Framework\Model\AbstractModel $bank */
			$bankBin = $this->bankBinInterface->create();
			$this->resource->load($bankBin, $bankBinId);
			if (!$bankBin->getId()) {
				throw new NoSuchEntityException(__('Requested Sprint Response doesn\'t exist'));
			}
			$this->instances[$bankBinId] = $bankBin;
		}
		return $this->instances[$bankBinId];
	}

	/**
	 * Retrieve pages matching the specified criteria.
	 *
	 * @param SearchCriteriaInterface $searchCriteria
	 * @return \Trans\Sprint\Api\Data\BankSearchResultsInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 * @SuppressWarnings(PHPMD)
	 */
	public function getList(SearchCriteriaInterface $searchCriteria) {
		/** @var \Trans\Sprint\Api\Data\BankSearchResultsInterface $searchResults */
		$searchResults = $this->searchResultsFactory->create();
		$searchResults->setSearchCriteria($searchCriteria);

		/** @var \Trans\Sprint\Model\ResourceModel\Bank\Collection $collection */
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

		/** @var \Trans\Sprint\Api\Data\BankBinInterface[] $bank */
		$bankBins = [];
		/** @var \Trans\Sprint\Model\Bank $bank */
		foreach ($collection as $bankBin) {
			/** @var \Trans\Sprint\Api\Data\BankBinInterface $bankDataObject */
			$bankBinDataObject = $this->bankBinInterface->create();
			$this->dataObjectHelper->populateWithArray($bankBinDataObject, $bankBin->getData(), BankBinInterface::class);
			$bankBins[] = $bankDataObject;
		}

		$searchResults->setTotalCount($collection->getSize());
		return $searchResults->setItems($bankBins);
	}

	/**
	 * Delete Sprint Response.
	 *
	 * @param \Trans\Sprint\Api\Data\BankBinInterface $bank
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(BankBinInterface $bankBin) {
		/** @var \Trans\Sprint\Api\Data\BankBinInterface|\Magento\Framework\Model\AbstractModel $bank */
		$bankBinId = $bankBin->getId();
		try {
			unset($this->instances[$bankBinId]);
			$this->resource->delete($bankBin);
		} catch (ValidatorException $e) {
			throw new CouldNotSaveException(__($e->getMessage()));
		} catch (\Exception $e) {
			throw new StateException(
				__('Unable to remove Bank Data %1', $bankBinId)
			);
		}
		unset($this->instances[$bankBinId]);
		return true;
	}

	/**
	 * Delete Sprint Response by ID.
	 *
	 * @param int $bankId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($bankBinId) {
		$bankBin = $this->getById($bankBinId);
		return $this->delete($bankBin);
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
