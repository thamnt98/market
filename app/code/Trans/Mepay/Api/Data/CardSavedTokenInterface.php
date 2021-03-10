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

namespace Trans\Mepay\Api\Data;

use Trans\Mepay\Helper\Data;

interface CardSavedTokenInterface 
{
    /**
     * @var array
     */
    const METHODS_LIST = Data::BANK_MEGA_PAYMENT_METHOD;

    /**
     * @var string
     */
    const METHOD = 'method';

    /**
     * @var string
     */
    const KEY = 'key';

    /**
     * @var string
     */
    const TOKEN = 'token';

    /**
     * @var string
     */
    const CARDTOKEN = 'cardtoken';

    /**
     * Get method key
     *
     * @return string[]
     */
    public static function getMethodKeys();

    /**
     * Set method
     *
     * @param string $method
     * @return void
     */
    public function setMethod(string $method);

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod();

    /**
     * Set key
     *
     * @param string $key
     * @return void
     */
    public function setKey(string $key);

    /**
     * Get key
     *
     * @return string
     */
    public function getKey();

    /**
     * Set token
     *
     * @param string $token
     * @return void
     */
    public function setToken(string $token);

    /**
     * Get token
     *
     * @return string
     */
    public function getToken();
}