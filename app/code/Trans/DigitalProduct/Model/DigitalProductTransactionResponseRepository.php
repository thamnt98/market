<?php
/**
 * @category Trans
 * @package  Trans_DigitalProduct
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\DigitalProduct\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Trans\DigitalProduct\Api\Data\DigitalProductTransactionResponseInterface;
use Trans\DigitalProduct\Api\Data\DigitalProductTransactionResponseInterfaceFactory;
use Trans\DigitalProduct\Api\DigitalProductTransactionResponseRepositoryInterface;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductTransactionResponse as TransactionResponse;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductTransactionResponse\Collection;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductTransactionResponse\CollectionFactory;

/**
 * Class DigitalProductTransactionResponseRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DigitalProductTransactionResponseRepository implements DigitalProductTransactionResponseRepositoryInterface {
	/**
	 * @var array
	 */
	protected $instances = [];

	/**
	 * @var TransactionResponse
	 */
	protected $resource;

	/**
	 * @var CollectionFactory
	 */
	protected $sprintResCollection;

	/**
	 * @var Collection
	 */
	protected $responseCollection;

	/**
	 * @var DigitalProductTransactionResponseInterfaceFactory
	 */
	protected $transactionResponseInterfaceFactory;

	/**
	 * @param TransactionResponse $resource
	 * @param CollectionFactory $sprintResCollection
	 * @param Collection $responseCollection
	 * @param DigitalProductTransactionResponseInterfaceFactory $transactionResponseInterfaceFactory
	 */
	public function __construct(
		TransactionResponse $resource,
		Collection $responseCollection,
		CollectionFactory $sprintResCollection,
		DigitalProductTransactionResponseInterfaceFactory $transactionResponseInterfaceFactory
	) {
		$this->resource                            = $resource;
		$this->responseCollection                  = $responseCollection;
		$this->sprintResCollection                 = $sprintResCollection;
		$this->transactionResponseInterfaceFactory = $transactionResponseInterfaceFactory;

	}

	/**
	 * Save page.
	 *
	 * @param \Trans\DigitalProduct\Api\Data\DigitalProductTransactionResponseInterface $transactionResponse
	 * @return \Trans\DigitalProduct\Api\Data\DigitalProductTransactionResponseInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(DigitalProductTransactionResponseInterface $transactionResponse) {
		/** @var \Trans\DigitalProduct\Api\Data\DigitalProductTransactionResponseInterfaceFactory|\Magento\Framework\Model\AbstractModel $transactionResponse */

		try {
			$this->resource->save($transactionResponse);
		} catch (\Exception $exception) {
			throw new CouldNotSaveException(__(
				'Could not save the Digital Product Transaction Response: %1',
				$exception->getMessage()
			));
		}
		return $transactionResponse;
	}

	/**
	 * Retrieve SprintResponse.
	 *
	 * @param int $transactionResponseId
	 * @return \Trans\DigitalProduct\Api\Data\DigitalProductTransactionResponseInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($transactionResponseId) {
		if (!isset($this->instances[$transactionResponseId])) {
			/** @var \Trans\DigitalProduct\Api\Data\DigitalProductTransactionResponseInterface|\Magento\Framework\Model\AbstractModel $transactionResponse */
			$transactionResponse = $this->transactionResponseInterfaceFactory->create();
			$this->resource->load($transactionResponse, $transactionResponseId);
			if (!$transactionResponse->getId()) {
				throw new NoSuchEntityException(__('Requested Digital Product Transaction Response doesn\'t exist'));
			}
			$this->instances[$transactionResponseId] = $transactionResponse;
		}
		return $this->instances[$transactionResponseId];
	}

	/**
	 * Delete Digital Product Transaction Response.
	 *
	 * @param \Trans\DigitalProduct\Api\Data\DigitalProductTransactionResponseInterface $transactionResponse
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(DigitalProductTransactionResponseInterface $transactionResponse) {
		/** @var \Trans\DigitalProduct\Api\Data\DigitalProductTransactionResponseInterface|\Magento\Framework\Model\AbstractModel $transactionResponse */
		$transactionResponseId = $transactionResponse->getId();
		try {
			unset($this->instances[$transactionResponseId]);
			$this->resource->delete($transactionResponse);
		} catch (ValidatorException $e) {
			throw new CouldNotSaveException(__($e->getMessage()));
		} catch (\Exception $e) {
			throw new StateException(
				__('Unable to remove Digital Product Transaction Response %1', $transactionResponseId)
			);
		}
		unset($this->instances[$transactionResponseId]);
		return true;
	}

	/**
	 * Delete Digital Product Transaction Response by ID.
	 *
	 * @param int $transactionResponseId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($transactionResponseId) {
		$transactionResponse = $this->getById($transactionResponseId);
		return $this->delete($transactionResponse);
	}
}
