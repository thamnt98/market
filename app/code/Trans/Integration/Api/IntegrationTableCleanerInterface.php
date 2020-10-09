<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Api;

interface IntegrationTableCleanerInterface
{
    /**
     * Error Message
     */
    const MSG_REQUIRE_PARAM= 'Parameter are empty !';
    
    /**
     * @param string $table
     * @param mixed $status string|int|array
     * @return void
     * @throws \Exception
     */
    public function cleanTableByStatus($table, $status);
}
