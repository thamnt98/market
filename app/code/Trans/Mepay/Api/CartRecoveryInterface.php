<?php
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author  Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Mepay\Api;

/**
 * CartRecovery interface.
 * @api
 */
interface CartRecoveryInterface
{
    /**
     * Execute cart recovery
     * @param  int $id
     * @return Trans\Mepay\Api\Data\CartRecoveryResultInterface
     */
    public function execute(int $id);
}
