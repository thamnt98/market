<?php

namespace SM\Checkout\Api;

/**
 * Interface for managing customers accounts.
 * @api
 */
interface AccountManagementInterface
{
    /**
     * @param mixed $address
     * @return \SM\Checkout\Api\Data\AccountManagementResponseInterface
     */
    public function save($address);
}
