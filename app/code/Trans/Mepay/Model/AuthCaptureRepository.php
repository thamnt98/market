<?php
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Mepay\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Store\Model\StoreManagerInterface;
use Trans\Mepay\Api\Data\AuthCaptureInterface;
use Trans\Mepay\Api\Data\AuthCaptureInterfaceFactory;
use Trans\Mepay\Api\AuthCaptureRepositoryInterface;
use Trans\Mepay\Model\ResourceModel\AuthCapture as ResourceAuthCapture;
use Trans\Mepay\Model\ResourceModel\AuthCapture\Collection;
use Trans\Mepay\Model\ResourceModel\AuthCapture\CollectionFactory as collectionFactoryFactory;

/**
 * Class AuthCaptureRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AuthCaptureRepository implements AuthCaptureRepositoryInterface
{
	/**
	 * @var array
	 */
	protected $instances = [];

	/**
	 * @var ResourceAuthCapture
	 */
	protected $resource;

	/**
	 * @var collectionFactoryFactory
	 */
	protected $collectionFactory;

	/**
	 * @var Collection
	 */
	protected $collection;

	/**
	 * @var AuthCaptureInterfaceFactory
	 */
	protected $authCaptureInterface;

	/**
	 * @var DataObjectHelper
	 */
	protected $dataObjectHelper;

	/**
	 * @var StoreManager
	 */
	protected $storeManager;

	/**
	 * @param ResourceAuthCapture $resource
	 * @param collectionFactoryFactory $collectionFactory
	 * @param Collection $collection
	 * @param AuthCaptureInterfaceFactory $authCaptureInterface
	 * @param DataObjectHelper $dataObjectHelper
	 * @param StoreManagerInterface $storeManager
	 */
	public function __construct(
		ResourceAuthCapture $resource,
		Collection $collection,
		collectionFactoryFactory $collectionFactory,
		AuthCaptureInterfaceFactory $authCaptureInterface,
		DataObjectHelper $dataObjectHelper,
		StoreManagerInterface $storeManager
	) {
		$this->resource = $resource;
		$this->collection = $collection;
		$this->collectionFactory  = $collectionFactory;
		$this->authCaptureInterface = $authCaptureInterface;
		$this->dataObjectHelper = $dataObjectHelper;
		$this->storeManager = $storeManager;
	}

	/**
	 * Save data.
	 *
	 * @param \Trans\Mepay\Api\Data\AuthCaptureInterface $authCapture
	 * @return \Trans\Mepay\Api\Data\AuthCaptureInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(AuthCaptureInterface $authCapture) {
		/** @var AuthCaptureInterface|\Magento\Framework\Model\AbstractModel $authCapture */

		try {
			$this->resource->save($authCapture);
		} catch (\Exception $exception) {
			throw new CouldNotSaveException(__(
				'Could not save the data: %1',
				$exception->getMessage()
			));
		}
		return $authCapture;
	}

	/**
	 * Retrieve AuthCapture.
	 *
	 * @param int $authCaptureId
	 * @return \Trans\Mepay\Api\Data\AuthCaptureInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($authCaptureId) {
		if (!isset($this->instances[$authCaptureId])) {
			/** @var \Trans\Mepay\Api\Data\AuthCaptureInterface|\Magento\Framework\Model\AbstractModel $authCapture */
			$authCapture = $this->authCaptureInterface->create();
			$this->resource->load($authCapture, $authCaptureId);
			if (!$authCapture->getId()) {
				throw new NoSuchEntityException(__('Requested data doesn\'t exist'));
			}
			$this->instances[$authCaptureId] = $authCapture;
		}
		return $this->instances[$authCaptureId];
	}

	/**
	 * Retrieve data reference number.
	 *
	 * @param int $refNumber
	 * @return \Trans\Mepay\Api\Data\AuthCaptureInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getByReferenceNumber($refNumber)
	{
		if (!isset($this->instances[$refNumber])) {
			/** @var \Trans\Mepay\Model\ResourceModel\AuthCapture\CollectionFactory|\Magento\Framework\Model\AbstractModel $customOrderItem */
			$authCapture = $this->authCaptureInterface->create();
			$this->resource->load($authCapture, $refNumber, AuthCaptureInterface::REFERENCE_NUMBER);

			if (!$authCapture->getId()) {
				throw new NoSuchEntityException(__('Requested data doesn\'t exist'));
			}

			$this->instances[$refNumber] = $authCapture;
		}

		return $this->instances[$refNumber];
	}

	/**
	 * Retrieve data By reference order id.
	 *
	 * @param int $refOrderId
	 * @return \Trans\Mepay\Api\Data\AuthCaptureInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getByReferenceOrderId($refOrderId) {
		if (!isset($this->instances[$refOrderId])) {
			/** @var \Trans\Mepay\Api\Data\AuthCaptureInterface|\Magento\Framework\Model\AbstractModel $authCapture */
			$authCapture = $this->authCaptureInterface->create();
			$this->resource->load($authCapture, $refOrderId, AuthCaptureInterface::REFERENCE_ORDER_ID);
			
			if (!$authCapture->getId()) {
				return $this->authCaptureInterface->create();
			}

			$this->instances[$refOrderId] = $authCapture;
		}

		return $this->instances[$refOrderId];
	}

	/**
	 * Delete data.
	 *
	 * @param \Trans\Mepay\Api\Data\AuthCaptureInterface $authCapture
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(AuthCaptureInterface $authCapture) {
		/** @var \Trans\Mepay\Api\Data\AuthCaptureInterface|\Magento\Framework\Model\AbstractModel $authCapture */
		$authCaptureId = $authCapture->getId();
		try {
			unset($this->instances[$authCaptureId]);
			$this->resource->delete($authCapture);
		} catch (ValidatorException $e) {
			throw new CouldNotSaveException(__($e->getMessage()));
		} catch (\Exception $e) {
			throw new StateException(
				__('Unable to remove data %1', $authCaptureId)
			);
		}
		unset($this->instances[$authCaptureId]);
		return true;
	}

	/**
	 * Delete data by ID.
	 *
	 * @param int $authCaptureId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($authCaptureId) {
		$authCapture = $this->getById($authCaptureId);
		return $this->delete($authCapture);
	}
}
