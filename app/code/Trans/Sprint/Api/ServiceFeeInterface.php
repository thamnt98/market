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
 * Service Fee interface.
 * @api
 */
interface ServiceFeeInterface
{
    /**
     * Returns information for a service fee in cart.
     *
     * @param int $cartId The cart ID.
     * @return string The service fee data.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified quote does not exist.
     */
    public function get($cartId);

    /**
     * Adds a service fee to a specified cart.
     *
     * @param int $cartId The cart ID.
     * @param int $term The service fee value.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified quote does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified coupon could not be added.
     */
    public function setServiceFee($cartId, $serviceFeeValue = 0);

    /**
     * Deletes a service fee from a specified cart.
     *
     * @param int $cartId The cart ID.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotDeleteException The specified coupon could not be deleted.
     */
    public function remove($cartId);
}
