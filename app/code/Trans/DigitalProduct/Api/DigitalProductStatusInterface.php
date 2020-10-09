<?php
/**
 * @category Trans
 * @package  Trans_DigitalProduct
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   hadi <ashadi.sejati@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\DigitalProduct\Api;

/**
 * @api
 */
interface DigitalProductStatusInterface
{

    /**
     * Status or callback from altera
     *
     * @param  string $dataReq
     * @return mixed
     */
    public function getCallbackAltera($dataReq);
}
