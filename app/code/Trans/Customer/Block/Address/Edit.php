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
 * Class Edit
 *
 */
class Edit extends \Magento\Customer\Block\Address\Edit
{
    /**
     * Return the title, either editing an existing address, or adding a new one.
     *
     * @return string
     */
    public function getTitle()
    {
        return __('My Address');
    }

    /**
     * Get Location
     *
     * @return array
     */
    public function getLocation()
    {
        $status = 0;
        $pinpoint = '';
        $lat = "";
        $lng = "";
        $customer = $this->getAddress();
        if ($customer->getCustomAttribute('pinpoint_location')) {
            $pinpoint = $customer->getCustomAttribute('pinpoint_location')->getValue();
            $status = 1;
        }

        if ($customer->getCustomAttribute('latitude')) {
            $lat = $customer->getCustomAttribute('latitude')->getValue();

        }
        if ($customer->getCustomAttribute('longitude')) {
            $lng = $customer->getCustomAttribute('longitude')->getValue();
        }

        if ($lat && $lng) {
            $status = 1;
        }

        return ['status' => $status, 'lat' => $lat, 'long' => $lng, 'pinpoint' => $pinpoint];
    }

    /**
     * Get District
     * @return string
     */
    public function getDistrict()
    {
        $district = '';
        $customer = $this->getAddress();
        if ($customer->getCustomAttribute('district')) {
            $district = $customer->getCustomAttribute('district')->getValue();
        }
        return $district;
    }

    /**
     * Get Address Tag
     * @return string
     */
    public function getAddressTag()
    {
        $addressTag = '';
        $customer = $this->getAddress();
        if ($customer->getCustomAttribute('address_tag')) {
            $addressTag = $customer->getCustomAttribute('address_tag')->getValue();
        }
        return $addressTag;
    }

    /**
     * Get Recipient Email
     * @return string
     */
    public function getRecipientEmail()
    {
        $recipientemail = '';
        $customer = $this->getAddress();
        if ($customer->getCustomAttribute('recipient_email')) {
            $recipientemail = $customer->getCustomAttribute('recipient_email')->getValue();
        }
        return $recipientemail;
    }

    /**
     * Get Customer Session Login Data
     *
     */
    public function getCustomerDeliveryData()
    {
        $customer = $this->_customerSession->getCustomer();
        return $customer;
    }

    /**
     * Get Full Name
     *
     * @return string|null
     */
    public function getFullName()
    {
        $customer = $this->getAddress();
        $firstName = $customer->getFirstname();
        $lastName = $customer->getLastname();
        if ($firstName == $lastName) {
            return $firstName;
        }
        return $firstName . ' ' . $lastName;
    }
}
