<?php
/**
 * Copyright © Metro-2021-noerakhiri All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Metro\CronStock\Api;

interface CronStockManagementInterface
{

    /**
     * GET for CronStock api
     * @param string $param
     * @return string
     */
    public function getCronStock($param);

    /**
     * GET for CronStockData api
     * @param string $param
     * @return string
     */
    public function getCronStockData($param);
}