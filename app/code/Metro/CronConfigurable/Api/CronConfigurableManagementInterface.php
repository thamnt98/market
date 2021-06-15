<?php
/**
 * Copyright © Metro-2021-noerakhiri All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Metro\CronConfigurable\Api;

interface CronConfigurableManagementInterface
{

    /**
     * GET for CronConfigurable api
     * @param string $param
     * @return string
     */
    public function getCronConfigurable($param);

    /**
     * GET for CronConfigurableData api
     * @param string $param
     * @return string
     */
    public function getCronConfigurableData($param);
}