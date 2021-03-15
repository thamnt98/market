<?php
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author  Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Mepay\Api;

/**
 * Webhook interface.
 * @api
 */
interface TokenizationInterface
{
    /**
     * Get Token List by Payment Method
     * @param  string $paymentCode
     * @return \Trans\Mepay\Api\Data\ResponseInterface
     */
    public function tokenlist(string $paymentCode);

    /**
     * Save Token
     * @param  string $token
     * @param  string $method
     * @return \Trans\Mepay\Api\Data\ResponseInterface
     */
    public function savetoken(string $token, string $method);
}
