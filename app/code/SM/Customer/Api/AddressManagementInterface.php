<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Customer
 *
 * Date: May, 06 2020
 * Time: 11:29 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Customer\Api;

use Magento\Framework\Exception\LocalizedException;

interface AddressManagementInterface
{
    /**
     * @param integer $customerId
     * @param \Magento\Customer\Api\Data\AddressInterface $addressData
     *
     * @return \Magento\Customer\Api\Data\AddressInterface
     */
    public function save($customerId, $addressData);

    /**
     * @param integer $customerId
     * @param integer $addressId
     * @return boolean
     */
    public function delete($customerId, $addressId);

    /**
     * @param int $customerId
     * @return boolean
     */
    public function validateMaxAddress($customerId);
}
