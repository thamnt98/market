<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCustomer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCustomer\Model;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use \Trans\IntegrationCustomer\Api\Data\IntegrationCustomerCentralInterface;
use \Trans\IntegrationCustomer\Api\Data\IntegrationCustomerCentralInterfaceFactory;
use \Trans\IntegrationCustomer\Api\Data\IntegrationCustomerCentralSearchResultInterface;
use \Trans\IntegrationCustomer\Api\Data\IntegrationCustomerCentralSearchResultInterfaceFactory;
use \Trans\IntegrationCustomer\Api\IntegrationCustomerCentralRepositoryInterface;
use \Trans\IntegrationCustomer\Model\ResourceModel\IntegrationCustomerCentral as ResourceModel;
use \Trans\IntegrationCustomer\Model\ResourceModel\IntegrationCustomerCentral\Collection;
use \Trans\IntegrationCustomer\Model\ResourceModel\IntegrationCustomerCentral\CollectionFactory;

class IntegrationCustomerCentralRepository implements IntegrationCustomerCentralRepositoryInterface {
	/**
	 * @var array
	 */
	protected $instances = [];

	/**
	 * @var ResourceModel
	 */
	protected $resource;

	/**
	 * @var CustomerCollection
	 */
	private $customerCollection;

	/**
	 * @var CollectionFactory
	 */
	private $collectionFactory;

	/**
	 * @var IntegrationCustomerCentralSearchResultInterfaceFactory
	 */
	private $searchResultFactory;

	/**
	 * @var IntegrationCustomerCentralInterfaceFactory
	 */
	protected $interface;

	/**
	 * IntegrationCustomerCentralRepository constructor.
	 * @param ResourceModel $resource
	 * @param CollectionFactory $collectionFactory
	 * @param IntegrationCustomerCentralSearchResultInterfaceFactory $searchResultFactory
	 * @param IntegrationCustomerCentralInterfaceFactory $interface
	 */
	public function __construct(
		ResourceModel $resource,
		CustomerCollection $customerCollection,
		CollectionFactory $collectionFactory,
		IntegrationCustomerCentralSearchResultInterfaceFactory $searchResultFactory,
		IntegrationCustomerCentralInterfaceFactory $interface
	) {
		$this->resource            = $resource;
		$this->customerCollection  = $customerCollection;
		$this->collectionFactory   = $collectionFactory;
		$this->searchResultFactory = $searchResultFactory;
		$this->interface           = $interface;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getById($id) {
		if (!isset($this->instances[$id])) {
			/** @var IntegrationCustomerCentralInterface|\Magento\Framework\Model\AbstractModel $data */
			$data = $this->interface->create();
			$this->resource->load($data, $id);
			if (!$data->getId()) {
				throw new NoSuchEntityException(__('Requested Data Reservation Response doesn\'t exist'));
			}
			$this->instances[$id] = $data;
		}
		return $this->instances[$id];
	}

	/**
	 * {@inheritdoc}
	 */
	public function save(IntegrationCustomerCentralInterface $data) {
		/** @var IntegrationCustomerCentralInterface|\Magento\Framework\Model\AbstractModel $data */
		try {
			$this->resource->save($data);
		} catch (\Exception $exception) {
			throw new CouldNotSaveException(__(
				'Could not save the data: %1',
				$exception->getMessage()
			));
		}
		return $data;
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete(IntegrationCustomerCentralInterface $data) {
		/** @var IntegrationCustomerCentralInterface|\Magento\Framework\Model\AbstractModel $data */
		$id = $data->getId();

		try {
			unset($this->instances[$id]);
			$this->resource->delete($data);
		} catch (ValidatorException $e) {
			throw new CouldNotSaveException(__($e->getMessage()));
		} catch (\Exception $e) {
			throw new StateException(
				__('Unable to remove data %1', $id)
			);
		}
		unset($this->instances[$id]);
		return true;
	}

	/**
	 * @param array $param
	 * @return mixed|IntegrationJobInterface
	 * @throws StateException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function saveData($param = []) {
		try {
			$this->validateParamArray($param);

		} catch (\Exception $ex) {
			throw new StateException(
				__($ex->getMessage())
			);
		}

		$data = $this->interface->create();
		try {
			if (isset($param[IntegrationCustomerCentralInterface::CUST_ID])) {
				$data->setMagentoCustomerId($param[IntegrationCustomerCentralInterface::CUST_ID]);
			}
			if (isset($param[IntegrationCustomerCentralInterface::CENTRAL_ID])) {
				$data->setCentralId($param[IntegrationCustomerCentralInterface::CENTRAL_ID]);
			}
			if (isset($param[IntegrationCustomerCentralInterface::STATUS])) {
				$data->setStatus($param[IntegrationCustomerCentralInterface::STATUS]);
			}

		} catch (\Exception $ex) {
			throw new StateException(
				__($ex->getMessage())
			);
		}
		$this->save($data);
		return $data;

	}

	/**
	 * @param array $param
	 * @throws StateException
	 */
	protected function validateParamArray($param = []) {
		if (!is_array($param)) {
			throw new StateException(__(
				'Parameter Jobs are empty !'
			));
		}
		$check = array_filter($param);
		if (empty($check)) {
			throw new StateException(__(
				'Parameter Jobs are empty !'
			));
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCustomerByEmail($email) {
		$collection = $this->customerCollection->create();
		$collection->addFieldToFilter("email", $email);

		return $collection->getFirstItem();
	}

}
