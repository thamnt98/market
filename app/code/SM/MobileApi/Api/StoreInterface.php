<?php

namespace SM\MobileApi\Api;

/**
 * Interface StoreInterface
 *
 * @package SM\MobileApi\Api
 */
interface StoreInterface
{
    /**
     * @param int||null $website_id
     * @return \SM\MobileApi\Api\Data\Store\StoreViewInterface[]
     */
    public function getList($website_id = null);
}
