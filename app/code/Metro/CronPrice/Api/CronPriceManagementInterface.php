<?php
/**
 * Copyright © Metro-2021 All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Metro\CronPrice\Api;

interface CronPriceManagementInterface
{

    /**
     * GET for CronPrice api
     * @param string $param
     * @return string
     */
    public function getCronPrice($param);

    /**
     * GET for CronPriceData api
     * @param string $param
     * @return string
     */
    public function getCronPriceData($param);
}