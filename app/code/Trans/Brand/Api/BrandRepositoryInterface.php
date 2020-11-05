<?php
/**
 * @category Trans
 * @package  Trans_Brand
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Brand\Api;

use Trans\Brand\Api\Data\BrandInterface;

interface BrandRepositoryInterface
{
  	/**
	 * Get by PIM Id
	 *
	 * @param  string $pimId
	 * @return BrandInterface
	 */
	public function getByPimId(string $pimId);

	/**
	 * Save Trans Brand
	 *
	 * @param  BrandInterface $data
	 * @return BrandInterface
	 */
	public function save(BrandInterface $data);
}
