<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
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
use Trans\Sprint\Api\Data\SprintCustomerTokenizationInterface;
use Trans\Sprint\Api\Data\SprintCustomerTokenizationInterfaceFactory;
use Trans\Sprint\Api\Data\SprintCustomerTokenizationSearchResultsInterfaceFactory;
use Trans\Sprint\Api\SprintCustomerTokenizationRepositoryInterface;
use Trans\Sprint\Model\ResourceModel\CustomerTokenization as ResourceTokenization;
use Trans\Sprint\Model\ResourceModel\CustomerTokenization\Collection;
use Trans\Sprint\Model\ResourceModel\CustomerTokenization\CollectionFactory as collectionFactory;

/**
 * Class CustomerTokenizationRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomerTokenizationRepository implements SprintCustomerTokenizationRepositoryInterface
{
	/**
	 * @var array
	 */
	protected $instances = [];

	/**
	 * @var \Magento\Quote\Api\CartRepositoryInterface
	 */
	protected $quoteRepository;

	/**
	 * @var ResourceModel
	 */
	protected $resource;

	/**
	 * @var CollectionFactory
	 */
	protected $collection;

	/**
	 * @var SprintCustomerTokenizationSearchResultsInterfaceFactory
	 */
	protected $searchResultsFactory;

	/**
	 * @var SprintCustomerTokenizationInterfaceFactory
	 */
	protected $tokenization;

	/**
	 * @var DataObjectHelper
	 */
	protected $dataObjectHelper;

	/**
	 * @var StoreManager
	 */
	protected $storeManager;

	/**
	 * @var \Trans\Sprint\Api\SprintResponseRepositoryInterface
	 */
	protected $sprintResponse;

	/**
	 * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
	 * @param ResourceTokenization $resource
	 * @param CollectionFactory $collection
	 * @param SprintCustomerTokenizationSearchResultsInterfaceFactory $searchResultsFactory
	 * @param SprintCustomerTokenizationInterfaceFactory $tokenization
	 * @param DataObjectHelper $dataObjectHelper
	 * @param StoreManagerInterface $storeManager
	 * @param \Trans\Sprint\Api\SprintResponseRepositoryInterface $sprintResponse
	 */
	public function __construct(
		\Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
		ResourceTokenization $resource,
		CollectionFactory $collection,
		SprintCustomerTokenizationSearchResultsInterfaceFactory $searchResultsFactory,
		SprintCustomerTokenizationInterfaceFactory $tokenization,
		DataObjectHelper $dataObjectHelper,
		StoreManagerInterface $storeManager,
		\Trans\Sprint\Api\SprintResponseRepositoryInterface $sprintResponse
	) {
		$this->quoteRepository = $quoteRepository;
		$this->resource = $resource;
		$this->collection = $collection;
		$this->searchResultsFactory = $searchResultsFactory;
		$this->tokenization = $tokenization;
		$this->dataObjectHelper = $dataObjectHelper;
		$this->storeManager = $storeManager;
		$this->sprintResponse = $sprintResponse;
	}

	/**
     * @inheritDoc
     */
	public function save(SprintCustomerTokenizationInterface $tokenization)
	{
		/** @var bankInterface|\Magento\Framework\Model\AbstractModel $tokenization */

		try {
			$this->resource->save($tokenization);
		} catch (\Exception $exception) {
			throw new CouldNotSaveException(__(
				'Could not save the Data: %1',
				$exception->getMessage()
			));
		}
		return $tokenization;
	}
	
	/**
     * @inheritDoc
     */
	public function getById($tokenizationId)
	{
		if (!isset($this->instances[$tokenizationId])) {
			/** @var \Trans\Sprint\Api\Data\SprintCustomerTokenizationInterface|\Magento\Framework\Model\AbstractModel $tokenization */
			$tokenization = $this->tokenization->create();
			$this->resource->load($tokenization, $tokenizationId);
			if (!$tokenization->getId()) {
				throw new NoSuchEntityException(__('Requested data doesn\'t exist'));
			}
			$this->instances[$tokenizationId] = $tokenization;
		}
		return $this->instances[$tokenizationId];
	}

	/**
     * @inheritDoc
     */
	public function getByCustomerId($customerId)
	{
		if (!isset($this->instances[$customerId])) {
			/** @var \Trans\Sprint\Api\Data\SprintCustomerTokenizationInterface|\Magento\Framework\Model\AbstractModel $tokenization */
			$tokenization = $this->tokenization->create();
			$this->resource->load($tokenization, $customerId, SprintCustomerTokenizationInterface::CUSTOMER_ID);
			
			if (!$tokenization->getSize()) {
				throw new NoSuchEntityException(__('Requested data doesn\'t exist'));
			}
			$this->instances[$customerId] = $tokenization;
		}
		return $this->instances[$customerId];
	}

	/**
     * @inheritDoc
     */
	public function getByMaskedCardNo($maskedCardNo)
	{
		if (!isset($this->instances[$maskedCardNo])) {
			/** @var \Trans\Sprint\Api\Data\SprintCustomerTokenizationInterface|\Magento\Framework\Model\AbstractModel $tokenization */
			$tokenization = $this->tokenization->create();
			$this->resource->load($tokenization, $maskedCardNo, SprintCustomerTokenizationInterface::MASKED_CARD_NO);
			
			if (!$tokenization->getId()) {
				throw new NoSuchEntityException(__('Requested data doesn\'t exist'));
			}
			$this->instances[$maskedCardNo] = $tokenization;
		}
		return $this->instances[$maskedCardNo];
	}

	/**
	 * @inheritDoc
	 */
	public function isCardTokenExists(int $customerId, string $maskedCard)
	{
		if (!isset($this->instances[$customerId . $maskedCard])) {
			/** @var \Trans\Sprint\Api\Data\SprintCustomerTokenizationInterface|\Magento\Framework\Model\AbstractModel $tokenization */
			$tokenization = $this->collection->create();
			$tokenization->addFieldToFilter(SprintCustomerTokenizationInterface::CUSTOMER_ID, $customerId);
			$tokenization->addFieldToFilter(SprintCustomerTokenizationInterface::MASKED_CARD_NO, $maskedCard);
			$token = $tokenization->getFirstItem();

			if ($token->getId()) {
				$this->instances[$customerId . $maskedCard] = true;
				return true;
			}
			$this->instances[$customerId . $maskedCard] = false;
		}

		return $this->instances[$customerId . $maskedCard];
	}

	/**
     * @inheritDoc
     */
	public function getList(SearchCriteriaInterface $searchCriteria)
	{
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

		/** @var \Trans\Sprint\Api\Data\SprintCustomerTokenizationInterface[] $tokenization */
		$tokenizations = [];
		/** @var \Trans\Sprint\Model\Bank $tokenization */
		foreach ($collection as $tokenization) {
			/** @var \Trans\Sprint\Api\Data\SprintCustomerTokenizationInterface $tokenizationDataObject */
			$tokenizationDataObject = $this->tokenization->create();
			$this->dataObjectHelper->populateWithArray($tokenizationDataObject, $tokenization->getData(), SprintCustomerTokenizationInterface::class);
			$tokenizations[] = $tokenizationDataObject;
		}

		$searchResults->setTotalCount($collection->getSize());
		return $searchResults->setItems($tokenizations);
	}

	/**
     * @inheritDoc
     */
	public function delete(SprintCustomerTokenizationInterface $tokenization)
	{
		/** @var \Trans\Sprint\Api\Data\SprintCustomerTokenizationInterface|\Magento\Framework\Model\AbstractModel $tokenization */
		$tokenizationId = $tokenization->getId();
		try {
			unset($this->instances[$tokenizationId]);
			$this->resource->delete($tokenization);
		} catch (ValidatorException $e) {
			throw new CouldNotSaveException(__($e->getMessage()));
		} catch (\Exception $e) {
			throw new StateException(
				__('Unable to remove Bank Data %1', $tokenizationId)
			);
		}
		unset($this->instances[$tokenizationId]);
		return true;
	}

	/**
     * @inheritDoc
     */
	public function deleteById($tokenizationId)
	{
		$tokenization = $this->getById($tokenizationId);
		return $this->delete($tokenization);
	}

	/**
	 * Helper function that adds a FilterGroup to the collection.
	 *
	 * @param FilterGroup $filterGroup
	 * @param Collection $collection
	 * @return $this
	 * @throws \Magento\Framework\Exception\InputException
	 */
	protected function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $collection)
	{
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
     * @inheritDoc
     */
	public function saveCardToken(string $transactionNo, string $maskedCardNo, string $cardToken)
	{
		try {
			$sprintResponse = $this->sprintResponse->getByTransactionNo($transactionNo);
			$quote = $this->quoteRepository->get($sprintResponse->getQuoteId());
			$customerId = $quote->getCustomerId();

			$isExists = $this->isCardTokenExists($customerId, $maskedCardNo);

			switch ($isExists) {
				case false:
					$tokenize = $this->tokenization->create();
					break;
				
				default:
					$tokenize = $this->getByMaskedCardNo($maskedCardNo);
					break;
			}
			
			$tokenize->setCustomerId($customerId);
			$tokenize->setMaskedCard($maskedCardNo);
			$tokenize->setCardToken($cardToken);

			$data = $this->save($tokenize);
			return $data;
		} catch (NoSuchEntityException $e) {
			throw new CouldNotSaveException(__('Could not save data. ' . $e->getMessage()));
		}
	}
}
