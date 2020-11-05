<?php
/**
 * @category Trans
 * @package  Trans_Brand
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2020 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Brand\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

use \Trans\Brand\Api\Data\BrandInterface;
use \Trans\Brand\Api\Data\BrandInterfaceFactory;
use \Trans\Brand\Api\BrandRepositoryInterface;
use \Trans\Brand\Model\ResourceModel\Brand as ResourceModel;
use \Trans\Brand\Model\ResourceModel\Brand\Collection;
use \Trans\Brand\Model\ResourceModel\Brand\CollectionFactory;

/**
 * Class BrandRepository
 */
class BrandRepository implements BrandRepositoryInterface
{
	/**
	 * @var array
	 */
	protected $instances = [];

	/**
	 * @var ResourceModel
	 */
	protected $resource;

	/**
	 * @var BrandCollectionFactory
	 */
	private $collectionFactory;

	/**
	 * @var BrandInterface
	 */
	protected $interface;

	/**
	 * BrandRepository constructor.
	 * @param ResourceModel $resource
	 * @param CollectionFactory $collectionFactory
	 * @param BrandInterfaceFactory $interface
	 */
	public function __construct(
		ResourceModel $resource,
		CollectionFactory $collectionFactory,
		BrandInterfaceFactory $interface
	) {
		$this->resource = $resource;
		$this->collectionFactory   = $collectionFactory;
		$this->interface           = $interface;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getByPimId(string $pimId)
	{
		if (!isset($this->instances[$pimId])) {
			/** @var BrandInterface|\Magento\Framework\Model\AbstractModel $data */
			$data = $this->interface->create();
			$this->resource->load($data, $pimId, BrandInterface::PIM_ID);

			if (!$data->getId()) {
				throw new NoSuchEntityException(__('Requested Data  doesn\'t exist'));
			}
			$this->instances[$pimId] = $data;
		}
		return $this->instances[$pimId];
	}

	/**
	 * {@inheritdoc}
	 */
	public function save(BrandInterface $data) {
		/** @var BrandInterface|\Magento\Framework\Model\AbstractModel $data */
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
}