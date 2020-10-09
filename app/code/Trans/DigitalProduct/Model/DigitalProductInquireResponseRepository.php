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
use Trans\DigitalProduct\Api\Data\DigitalProductInquireResponseInterface;
use Trans\DigitalProduct\Api\Data\DigitalProductInquireResponseInterfaceFactory;
use Trans\DigitalProduct\Api\DigitalProductInquireResponseRepositoryInterface;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductInquireResponse as InquireResponse;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductInquireResponse\Collection;
use Trans\DigitalProduct\Model\ResourceModel\DigitalProductInquireResponse\CollectionFactory;

/**
 * Class DigitalProductInquireResponseRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DigitalProductInquireResponseRepository implements DigitalProductInquireResponseRepositoryInterface {
	/**
	 * @var array
	 */
	protected $instances = [];

	/**
	 * @var InquireResponse
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
	 * @var DigitalProductInquireResponseInterfaceFactory
	 */
	protected $inquireResponseInterfaceFactory;

	/**
	 * @param InquireResponse $resource
	 * @param CollectionFactory $sprintResCollection
	 * @param Collection $responseCollection
	 * @param DigitalProductInquireResponseInterfaceFactory $inquireResponseInterfaceFactory
	 */
	public function __construct(
		InquireResponse $resource,
		Collection $responseCollection,
		CollectionFactory $sprintResCollection,
		DigitalProductInquireResponseInterfaceFactory $inquireResponseInterfaceFactory
	) {
		$this->resource                        = $resource;
		$this->responseCollection              = $responseCollection;
		$this->sprintResCollection             = $sprintResCollection;
		$this->inquireResponseInterfaceFactory = $inquireResponseInterfaceFactory;

	}

	/**
	 * Save page.
	 *
	 * @param \Trans\DigitalProduct\Api\Data\DigitalProductInquireResponseInterface $inquireResponse
	 * @return \Trans\DigitalProduct\Api\Data\DigitalProductInquireResponseInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(DigitalProductInquireResponseInterface $inquireResponse) {
		/** @var inquireResponseInterfaceFactory|\Magento\Framework\Model\AbstractModel $inquireResponse */

		try {
			$this->resource->save($inquireResponse);
		} catch (\Exception $exception) {
			throw new CouldNotSaveException(__(
				'Could not save the Digital Product Inquire Response: %1',
				$exception->getMessage()
			));
		}
		return $inquireResponse;
	}

	/**
	 * Retrieve SprintResponse.
	 *
	 * @param int $inquireResponseId
	 * @return \Trans\DigitalProduct\Api\Data\DigitalProductInquireResponseInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($inquireResponseId) {
		if (!isset($this->instances[$inquireResponseId])) {
			/** @var \Trans\DigitalProduct\Api\Data\DigitalProductInquireResponseInterface|\Magento\Framework\Model\AbstractModel $inquireResponse */
			$inquireResponse = $this->inquireResponseInterfaceFactory->create();
			$this->resource->load($inquireResponse, $inquireResponseId);
			if (!$inquireResponse->getId()) {
				throw new NoSuchEntityException(__('Requested Digital Product Inquire Response doesn\'t exist'));
			}
			$this->instances[$inquireResponseId] = $inquireResponse;
		}
		return $this->instances[$inquireResponseId];
	}

	/**
	 * Delete Digital Product Inquire Response.
	 *
	 * @param \Trans\DigitalProduct\Api\Data\DigitalProductInquireResponseInterface $inquireResponse
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(DigitalProductInquireResponseInterface $inquireResponse) {
		/** @var \Trans\DigitalProduct\Api\Data\DigitalProductInquireResponseInterface|\Magento\Framework\Model\AbstractModel $inquireResponse */
		$inquireResponseId = $inquireResponse->getId();
		try {
			unset($this->instances[$inquireResponseId]);
			$this->resource->delete($inquireResponse);
		} catch (ValidatorException $e) {
			throw new CouldNotSaveException(__($e->getMessage()));
		} catch (\Exception $e) {
			throw new StateException(
				__('Unable to remove Digital Product Inquire Response %1', $inquireResponseId)
			);
		}
		unset($this->instances[$inquireResponseId]);
		return true;
	}

	/**
	 * Delete Digital Product Inquire Response by ID.
	 *
	 * @param int $inquireResponseId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($inquireResponseId) {
		$inquireResponse = $this->getById($inquireResponseId);
		return $this->delete($inquireResponse);
	}
}
