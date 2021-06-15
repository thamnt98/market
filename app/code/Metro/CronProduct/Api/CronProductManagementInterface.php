<?php
/**
 * Copyright © Metro-2021 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Metro\CronProduct\Api;

interface CronProductManagementInterface
{

    /**
     * GET for CronProduct api
     * 
     * @return string
     */
    public function getCronProduct();
    /**
     * GET for CronProductData api
     * 
     * @return string
     */
    public function getCronProductData();
}
