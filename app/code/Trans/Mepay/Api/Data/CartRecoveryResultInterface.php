<?php

/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author  anan fauzi <anan.fauzi@ctcorpdigital.com>
 *
 * Copyright © 2020 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Mepay\Api\Data;

interface CartRecoveryResultInterface
{
    /**
     * @var string
     */
    const MESSAGE = 'message';

    /**
     * @var string
     */
    const STATUS = 'status';

    /**
     * Set message
     * @param string $message
     * @return void
     */
    public function setMessage(string $message);

    /**
     * Get message
     * @return string
     */
    public function getMessage();

    /**
     * Set status
     * @param string $status
     * @return void
     */
    public function setStatus(string $status);

    /**
     * Get status
     * @return string
     */
    public function getStatus();
}
