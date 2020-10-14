<?php
/**
 * @category    SM
 * @package     SM_Customer
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Nam Nguyen <namnd2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace Trans\Customer\Block\Address;

/**
 * Customer address grid
 *
 * @api
 * @since 102.0.1
 */
class Grid extends \Magento\Customer\Block\Address\Grid
{
    /**
     * @param mixed $customer
     * @return string
     */
    public function getCityAddress($customer)
    {
        $city = '';
        $cityObj = $customer->getCustomAttribute('city');
        if ($cityObj) {
            $city = $cityObj->getValue();
        }
        return $city;
    }

    /**
     * @param mixed $customer
     * @return string
     */
    public function getDistrictAddress($customer)
    {
        $district = '';
        $districtObj = $customer->getCustomAttribute('district');
        if ($districtObj) {
            $district = $districtObj->getValue();
        }
        return $district;
    }

    /**
     * @param mixed $customer
     * @return string
     */
    public function getTelephoneNumber($customer)
    {
        $telephone = '';
        $telephoneObj = $customer->getCustomAttribute('telephone');
        if ($telephoneObj) {
            $telephone = '08' . preg_replace("/^(^\+628|^628|^08|^8)/", '', $telephoneObj->getValue());
        }
        return $telephone;
    }

    /**
     * @param mixed $customer
     * @return string
     */
    public function getFullName($customer)
    {
        if ($customer->getFirstname() == $customer->getLastname()) {
            return $customer->getFirstname();
        }
        return $customer->getFirstname() . ' ' . $customer->getLastname();
    }
}
