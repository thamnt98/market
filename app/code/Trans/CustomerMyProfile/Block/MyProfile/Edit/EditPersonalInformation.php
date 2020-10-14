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

namespace Trans\CustomerMyProfile\Block\MyProfile\Edit;

/**
 * Class EditPersonalInformation
 */
class EditPersonalInformation extends \Magento\Framework\View\Element\Template
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
     * @var \Magento\Customer\Api\CustomerMetadataInterface
     */
    protected $customerMetadata;

    /**
     * @var \Trans\CustomerMyProfile\Helper\Data
     */
    protected $customerMyProfileHelper;

    /**
     * EditPersonalInformation constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Customer\Api\CustomerMetadataInterface $customerMetadata
     * @param \Trans\CustomerMyProfile\Helper\Data $customerMyProfileHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Api\CustomerMetadataInterface $customerMetadata,
        \Trans\CustomerMyProfile\Helper\Data $customerMyProfileHelper,
        array $data = []
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->eavConfig = $eavConfig;
        $this->customerMetadata = $customerMetadata;
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
     * Check if dob attribute enabled in system
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isDobEnable()
    {
        $attribute = $this->customerMetadata->getAttributeMetadata('dob');
        return $attribute ? (bool)$attribute->isVisible() : false;
    }

    /**
     * Check if gender attribute enabled in system
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isGenderEnable()
    {
        $attribute = $this->customerMetadata->getAttributeMetadata('gender');
        return $attribute ? (bool)$attribute->isVisible() : false;
    }

    /**
     * Get gender value
     *
     * @return string
     */
    public function getGenderValue()
    {
        $gender = '';

        $customer = $this->getCustomer();
        if ($customer->getGender()) {
            $gender = $customer->getGender();
        }

        return $gender;
    }

    /**
     * Get gender options
     *
     * @return array
     */
    public function getGenderOptions()
    {
        $genderOptions = [];

        $attribute = $this->eavConfig->getAttribute('customer', 'gender');
        $options = $attribute->getSource()->getAllOptions();

        foreach ($options as $option => $value) {
            if ($value['label'] != 'Not Specified') {
                $genderData = ['label' => $value['label'], 'value' => $value['value']];
                $genderOptions[] = $genderData;
            }
        }

        return $genderOptions;
    }

    /**
     * Get marital status value
     *
     * @return string
     */
    public function getMaritalStatusValue()
    {
        $maritalStatus = '';

        $customer = $this->getCustomer();
        if ($customer->getCustomAttribute('marital_status')) {
            $maritalStatus = $customer->getCustomAttribute('marital_status')->getValue();
        }

        return $maritalStatus;
    }

    /**
     * Get marital status options
     *
     * @return array
     */
    public function getMaritalStatusOptions()
    {
        $attribute = $this->eavConfig->getAttribute('customer', 'marital_status');
        return $attribute->getSource()->getAllOptions();
    }

    /**
     * Get nik value
     *
     * @return string
     */
    public function getNik()
    {
        $nik = '';
        $customer = $this->getCustomer();
        if ($customer->getCustomAttribute('nik')) {
            $nik = $customer->getCustomAttribute('nik')->getValue();
        }

        return $nik;
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
        $customer = $this->getCustomer();
        if ($customer->getCustomAttribute('profile_picture')) {
            $profile_picture = $this->getUrl('customermyprofile/myprofile/profilepictureview/', ['image' => base64_encode($customer->getCustomAttribute('profile_picture')->getValue())]);
        } else {
            $profile_picture = $this->getViewFileUrl('Trans_CustomerMyProfile::images/no-profile-photo.png');
        }

        return $profile_picture;
    }
}
