<?php
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Mepay\Model;

use Magento\Framework\DataObject;
use Trans\Mepay\Api\Data\CardSavedTokenInterface;

class CardSavedToken extends DataObject implements CardSavedTokenInterface
{
    /**
     * Get method key
     *
     * @return string[]
     */
    public static function getMethodKeys()
    {
        $result = [];
        foreach (self::METHODS_LIST as $key => $value) {
            $result[$value] = $value.'_'.self::TOKEN;
        }
        return $result;
    }

    /**
     * Set method
     *
     * @param string $method
     * @return void
     */
    public function setMethod(string $method)
    {
        $this->setData(self::METHOD, $method);
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->_getData(self::METHOD);
    }

    /**
     * Set key
     *
     * @param string $key
     * @return void
     */
    public function setKey(string $key)
    {
        $this->setData(self::KEY, $key);
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->_getData(self::KEY);
    }

    /**
     * Set token
     *
     * @param string $token
     * @return void
     */
    public function setToken(string $token)
    {
        $this->setData(self::TOKEN, $token);
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->getData(self::TOKEN);
    }
}