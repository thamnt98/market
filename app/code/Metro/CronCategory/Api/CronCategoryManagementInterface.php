<?php
/**
 * Copyright © Metro-2021-noerakhiri All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Metro\CronCategory\Api;

interface CronCategoryManagementInterface
{

    /**
     * GET for CronCategory api
     * @param string $param
     * @return string
     */
    public function getCronCategory($param);

     /**
     * GET for CronCategoryData api
     * @param string $param
     * @return string
     */
    public function getCronCategoryData($param);
}
