<?php

/**
 * @category SM
 * @package SM_Brand
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author      Chinhvd <chinhvd@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Brand\Api;

interface BrandRepositoryInterface
{
    /**
     * @param int $brandId
     * @return \SM\MobileApi\Api\Data\Product\ListInterface
     */
    public function getBrandProduct($brandId);

    /**
     * @param int $brandId
     * @return \SM\Brand\Api\Data\BrandInterface[]
     */
    public function getBrandCms($brandId);
}
