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

interface CardSavedTokenRepoInterface
{
    /**
     * Get token list
     *
     * @return \Trans\Mepay\Api\Data\CardSavedTokenInterface[]
     * @throws \Exception
     */
    public function getAll();

    /**
     * Save all token list
     *
     * @param \Trans\Mepay\Api\Data\CardSavedTokenInterface[] $list
     * @return \Trans\Mepay\Api\Data\CardSavedTokenInterface[]
     * @throws \Exception
     */
    public function saveAll($list);

    /**
     * Delete all token list
     *
     * @param \Trans\Mepay\Api\Data\CardSavedTokenInterface[] $list
     * @return bool
     * @throws \Exception
     */
    public function deleteAll($list);

    /**
     * Get token list by payment method
     *
     * @param string $method
     * @return \Trans\Mepay\Api\Data\CardSavedTokenInterface[]
     * @throws \Exception
     */
    public function getBymethod($method);

    /**
     * Save token 
     *
     * @param \Trans\Mepay\Api\Data\CardSavedTokenInterface $obj
     * @return \Trans\Mepay\Api\Data\CardSavedTokenInterface
     * @throws \Exception
     */
    public function save($obj);

    /**
     * Delete token
     *
     * @param \Trans\Mepay\Api\Data\CardSavedTokenInterface $obj
     * @return bool
     * @throws \Exception
     */
    public function delete($obj);

}