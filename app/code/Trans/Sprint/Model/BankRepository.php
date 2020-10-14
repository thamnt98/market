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
use Trans\Sprint\Api\Data\BankInterface;
use Trans\Sprint\Api\Data\BankInterfaceFactory;
use Trans\Sprint\Api\Data\BankSearchResultsInterfaceFactory;
use Trans\Sprint\Api\BankRepositoryInterface;
use Trans\Sprint\Model\ResourceModel\Bank as ResourceBank;
use Trans\Sprint\Model\ResourceModel\Bank\Collection;
use Trans\Sprint\Model\ResourceModel\Bank\CollectionFactory as collectionFactory;

/**
 * Class BankRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BankRepository implements BankRepositoryInterface {
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
	 * @var BankSearchResultsInterfaceFactory
	 */
	protected $searchResultsFactory;

	/**
	 * @var BankInterfaceFactory
	 */
	protected $bankInterface;

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

	 * @param BankSearchResultsInterfaceFactory $searchResultsFactory
	 * @param BankInterfaceFactory $bankInterface
	 * @param DataObjectHelper $dataObjectHelper
	 * @param StoreManagerInterface $storeManager
	 */
	public function __construct(
		ResourceBank $resource,
		CollectionFactory $collection,
		BankSearchResultsInterfaceFactory $searchResultsFactory,
		BankInterfaceFactory $bankInterface,
		DataObjectHelper $dataObjectHelper,
		StoreManagerInterface $storeManager
	) {
		$this->resource             = $resource;
		$this->collection  			= $collection;
		$this->searchResultsFactory = $searchResultsFactory;
		$this->bankInterface   		= $bankInterface;
		$this->dataObjectHelper     = $dataObjectHelper;
		$this->storeManager         = $storeManager;
	}

	/**
	 * Save page.
	 *
	 * @param \Trans\Sprint\Api\Data\BankInterface $bank
	 * @return \Trans\Sprint\Api\Data\BankInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(BankInterface $bank) {
		/** @var bankInterface|\Magento\Framework\Model\AbstractModel $bank */

		try {
			$this->resource->save($bank);
		} catch (\Exception $exception) {
			throw new CouldNotSaveException(__(
				'Could not save the Bank Data: %1',
				$exception->getMessage()
			));
		}
		return $bank;
	}

	/**
	 * Retrieve bank.
	 *
	 * @param int $bankId
	 * @return \Trans\Sprint\Api\Data\BankInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($bankId) {
		if (!isset($this->instances[$bankId])) {
			/** @var \Trans\Sprint\Api\Data\BankInterface|\Magento\Framework\Model\AbstractModel $bank */
			$bank = $this->bankInterface->create();
			$this->resource->load($bank, $bankId);
			if (!$bank->getId()) {
				throw new NoSuchEntityException(__('Requested Sprint Response doesn\'t exist'));
			}
			$this->instances[$bankId] = $bank;
		}
		return $this->instances[$bankId];
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

		/** @var \Trans\Sprint\Api\Data\BankInterface[] $bank */
		$banks = [];
		/** @var \Trans\Sprint\Model\Bank $bank */
		foreach ($collection as $bank) {
			/** @var \Trans\Sprint\Api\Data\BankInterface $bankDataObject */
			$bankDataObject = $this->bankInterface->create();
			$this->dataObjectHelper->populateWithArray($bankDataObject, $bank->getData(), BankInterface::class);
			$banks[] = $bankDataObject;
		}

		$searchResults->setTotalCount($collection->getSize());
		return $searchResults->setItems($banks);
	}

	/**
	 * Delete Sprint Response.
	 *
	 * @param \Trans\Sprint\Api\Data\BankInterface $bank
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(BankInterface $bank) {
		/** @var \Trans\Sprint\Api\Data\BankInterface|\Magento\Framework\Model\AbstractModel $bank */
		$bankId = $bank->getId();
		try {
			unset($this->instances[$bankId]);
			$this->resource->delete($bank);
		} catch (ValidatorException $e) {
			throw new CouldNotSaveException(__($e->getMessage()));
		} catch (\Exception $e) {
			throw new StateException(
				__('Unable to remove Bank Data %1', $bankId)
			);
		}
		unset($this->instances[$bankId]);
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
	public function deleteById($bankId) {
		$bank = $this->getById($bankId);
		return $this->delete($bank);
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

	/**
	 * {@inheritdoc}
	 */
	public function loadDataByName($name="") {
	
		if(empty($name)){
			throw new StateException(__(
				'Parameter Name are empty !'
			));
		}
		
		$collection = $this->bankInterface->create()->getCollection();
		$collection->addFieldToFilter(BankInterface::NAME, $name);

		$getLastCollection =NULL;
		if($collection->getSize()){
            $getLastCollection = $collection->getFirstItem();
            
        }
		return $getLastCollection;
	}
}
