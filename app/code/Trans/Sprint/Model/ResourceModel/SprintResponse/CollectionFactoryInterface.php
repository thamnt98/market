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
 * Class CollectionFactoryInterface
 */
interface CollectionFactoryInterface {
	/**
	 * Create class instance with specified parameters
	 *
	 * @param int $quoteId
	 * @param int $transNo
	 * @param int $storeId
	 * @return \Trans\Sprint\Model\ResourceModel\SprintResponse\Collection
	 */
	public function create($quoteId = null, $transNo = null, $storeId = null);
}
