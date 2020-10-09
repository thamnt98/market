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
namespace Trans\Sprint\Model\ResourceModel\SprintPaymentFlag;

/**
 * Class CollectionFactoryInterface
 */
interface CollectionFactoryInterface {
	/**
	 * Create class instance with specified parameters
	 *
	 * @param int $transNo
	 * @return \Trans\Sprint\Model\ResourceModel\SprintPaymentFlag\Collection
	 */
	public function create($transNo = null);
}
