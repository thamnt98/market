<?php

namespace SM\Customer\Api;

/**
 * Interface providing token generation for Customers
 *
 * @api
 * @since 100.0.2
 */
interface CustomerTokenServiceInterface
{
    /**
     * Create access token for admin given the customer credentials.
     *
     * @param string $identifier
     * @param string $type
     * @return string Token created
     * @throws \Magento\Framework\Exception\AuthenticationException
     */
    public function createCustomerAccessToken($identifier, $type);

    /**
     * Revoke token by customer id.
     *
     * @param int $customerId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function revokeCustomerAccessToken($customerId);
}
