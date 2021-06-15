<?php
/**
 * Copyright © Metro-2021-noerakhiri All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Metro\CronPromo\Api;

interface CronPromoManagementInterface
{

    /**
     * GET for CronPromo api
     * @param string $param
     * @return string
     */
    public function getCronPromo($param);

    /**
     * GET for CronPromoData api
     * @param string $param
     * @return string
     */
    public function getCronPromoData($param);
}