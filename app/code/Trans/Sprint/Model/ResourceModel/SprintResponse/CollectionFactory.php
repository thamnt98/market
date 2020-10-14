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
namespace Trans\Sprint\Model\ResourceModel\SprintResponse;

/**
 * Class CollectionFactory
 */
class CollectionFactory implements CollectionFactoryInterface {
	/**
	 * @var \Trans\Sprint\Model\ResourceModel\SprintResponse\Collection
	 */
	private $collection;

	/**
	 * Factory constructor
	 *
	 * @param \Trans\Sprint\Model\ResourceModel\SprintResponse\Collection $collection
	 */
	public function __construct(
		\Trans\Sprint\Model\ResourceModel\SprintResponse\Collection $collection
	) {
		$this->collection = $collection;
	}

	/**
	 * {@inheritdoc}
	 */
	public function create($quoteId = null, $transNo = null, $storeId = null) {
		/** @var \Trans\Sprint\Model\ResourceModel\SprintResponse\Collection $collection */
		$collection = $this->collection;

		if ($quoteId) {
			$collection->addFieldToFilter('quote_id', $quoteId);
		}

		if ($transNo) {
			$collection->addFieldToFilter('transaction_no', $transNo);
		}

		if ($storeId) {
			$collection->addFieldToFilter('store_id', $storeId);
		}

		return $collection;
	}
}
