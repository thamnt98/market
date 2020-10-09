<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Api;

/**
 * Auto Cancel interface.
 * @api
 */
interface AutoCancelInterface
{
    /**
     * Process auto cancel by payements code
     *
     * @param array $paymentCodes.
     * @return void.
     * @throws \Exception
     */
    public function cancelExpiredOrder(array $paymentCodes);
}
