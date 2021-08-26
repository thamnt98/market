<?php
/**
 * @category Trans
 * @package  Trans_CustomerMyProfile
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\CustomerMyProfile\Block\MyProfile;

/**
 * Class PersonalInformation
 */
class PersonalInformation extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @param \Trans\Core\Helper\Customer
     */
    protected $customerHelper;

    /**
     * @param \Trans\CustomerMyProfile\Helper\Data
     */
    protected $customerMyProfileHelper;

    /**
     * PersonalInformation constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Trans\Core\Helper\Customer $customerHelper
     * @param \Trans\CustomerMyProfile\Helper\Data $customerMyProfileHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Eav\Model\Config $eavConfig,
        \Trans\Core\Helper\Customer $customerHelper,
        \Trans\CustomerMyProfile\Helper\Data $customerMyProfileHelper,
        array $data = []
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->eavConfig = $eavConfig;
        $this->customerHelper = $customerHelper;
        $this->customerMyProfileHelper = $customerMyProfileHelper;
        parent::__construct($context, $data);
    }

    /**
     * Returns the Magento Customer Model for this block
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer()
    {
        try {
            return $this->currentCustomer->getCustomer();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get configuration max size profile picture
     *
     * @return int
     */
    public function getConfigMaxsize()
    {
        $maxsize = 1048576;
        if ($this->customerMyProfileHelper->getMaxsizeProfilePicture() != '') {
            $configMaxsize = $this->customerMyProfileHelper->getMaxsizeProfilePicture();
            $maxsize = 1048576 * $configMaxsize;
        }

        return $maxsize;
    }

    /**
     * Get profile picture url
     *
     * @return string
     */
    public function getProfilePicture()
    {
        $profile_picture = $this->getViewFileUrl('Trans_CustomerMyProfile::images/no-profile-photo.png');
        $customer = $this->getCustomer();
        if ($customer->getCustomAttribute('profile_picture')) {
            $profile_picture = $this->getUrl('customermyprofile/myprofile/profilepictureview/', ['image' => base64_encode($customer->getCustomAttribute('profile_picture')->getValue())]);
        }

        return $profile_picture;
    }

    /**
     * Get fullname
     *
     * @return string
     */
    public function getFullName()
    {
        $fullName = '-';
        $firstName = $this->getCustomer()->getFirstname();
        $lastMame = $this->getCustomer()->getLastname();
        $names = [$firstName, $lastMame];
        if ($names) {
            $fullName = $this->customerHelper->generateFullnameByArray($names);
        }
        return $fullName;
    }

    /**
     * Get date of birth
     *
     * @return string
     */
    public function getDateOfBirth()
    {
        $dob = '-';
        $customer = $this->getCustomer();
        if ($customer->getDob()) {
            $dob = $customer->getDob();
            $dob = date("j F Y", strtotime($dob));
        }

        return $dob;
    }

    /**
     * Get gender label
     *
     * @return string
     */
    public function getGenderLabel()
    {
        $gender = '-';

        $attribute = $this->eavConfig->getAttribute('customer', 'gender');
        $options = $attribute->getSource()->getAllOptions();

        $customer = $this->getCustomer();
        if ($customer->getGender()) {
            $gender = $customer->getGender();
            foreach ($options as $option => $value) {
                if ($value['value'] == $gender) {
                    $gender = $value['label'];
                }
            }
        }

        return $gender;
    }

    /**
     * Get nik value
     *
     * @return string
     */
    public function getNik()
    {
        $nik = '-';
        $customer = $this->getCustomer();
        if ($customer->getCustomAttribute('nik')) {
            $nik = $customer->getCustomAttribute('nik')->getValue();
        }

        return $nik;
    }

    /**
     * Get marital status label
     *
     * @return string
     */
    public function getMaritalStatusLabel()
    {
        $marital_status = '-';

        $attribute = $this->eavConfig->getAttribute('customer', 'marital_status');
        $options = $attribute->getSource()->getAllOptions();

        $customer = $this->getCustomer();
        if ($customer->getCustomAttribute('marital_status')) {
            $marital_status = $customer->getCustomAttribute('marital_status')->getValue();
            foreach ($options as $option => $value) {
                if ($value['value'] == $marital_status) {
                    $marital_status = $value['label'];
                }
            }
        }

        return $marital_status;
    }

    /**
     * Get marital status options
     *
     * @return array
     */
    public function getOmniStoreId()
    {
        $customer = $this->getCustomer();
        $omni_store = $customer->getCustomAttribute('omni_store_id');
        if (!empty($omni_store)) {
            return $omni_store->getValue();
        }
        return null;
    }

    /**
     * @return mixed|string
     */
    public function getOccupancy()
    {
        $occupancy = '-';
        $customer = $this->getCustomer();
        if ($customer->getCustomAttribute('occupancy')) {
            $occupancy = $customer->getCustomAttribute('occupancy')->getValue();
        }
        return $occupancy;
    }
}
