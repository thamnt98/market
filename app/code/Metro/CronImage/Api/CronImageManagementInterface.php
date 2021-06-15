<?php
/**
 * Copyright © Metro-2021-noerakhiri All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Metro\CronImage\Api;

interface CronImageManagementInterface
{

    /**
     * GET for CronImage api
     * @param string $param
     * @return string
     */
    public function getCronImage($param);

    /**
     * GET for CronImageData api
     * @param string $param
     * @return string
     */
    public function getCronImageData($param);
}