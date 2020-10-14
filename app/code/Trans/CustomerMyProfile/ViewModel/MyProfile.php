<?php

namespace Trans\CustomerMyProfile\ViewModel;

class MyProfile implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Trans\CustomerMyProfile\Helper\Data
     */
    protected $customerMyProfileHelper;

    /**
     * MyProfile constructor.
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Trans\CustomerMyProfile\Helper\Data $customerMyProfileHelper
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Eav\Model\Config $eavConfig,
        \Trans\CustomerMyProfile\Helper\Data $customerMyProfileHelper
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->assetRepo = $assetRepo;
        $this->eavConfig = $eavConfig;
        $this->customerMyProfileHelper = $customerMyProfileHelper;
    }

    /**
     * @param $customer
     * @return string
     */
    public function getProfilePicture($customer)
    {
        if ($customer->getCustomAttribute('profile_picture')) {
            $profile_picture = $this->urlBuilder->getUrl('customermyprofile/myprofile/profilepictureview/', ['image' => base64_encode($customer->getCustomAttribute('profile_picture')->getValue())]);
        } else {
            $profile_picture = $this->assetRepo->getUrlWithParams('Trans_CustomerMyProfile::images/no-profile-photo.png', []);
        }

        return $profile_picture;
    }

    /**
     * @param $customer
     * @return string
     */
    public function getGenderValue($customer)
    {
        $gender = '';
        if ($customer->getGender()) {
            $gender = $customer->getGender();
        }
        return $gender;
    }

    /**
     * @param $customer
     * @return string
     */
    public function getMaritalStatusValue($customer)
    {
        $maritalStatus = '';
        if ($customer->getCustomAttribute('marital_status')) {
            $maritalStatus = $customer->getCustomAttribute('marital_status')->getValue();
        }

        return $maritalStatus;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMaritalStatusOptions()
    {
        $attribute = $this->eavConfig->getAttribute('customer', 'marital_status');
        return $attribute->getSource()->getAllOptions();
    }

    /**
     * @param $customer
     * @return string
     */
    public function getNik($customer)
    {
        $nik = '';
        if ($customer->getCustomAttribute('nik')) {
            $nik = $customer->getCustomAttribute('nik')->getValue();
        }

        return $nik;
    }

    /**
     * @return float|int
     */
    public function getConfigMaxsize()
    {
        $maxsize = 1048576;
        $configMaxsize = 1;
        if ($this->customerMyProfileHelper->getMaxsizeProfilePicture() != '') {
            $configMaxsize = $this->customerMyProfileHelper->getMaxsizeProfilePicture();
            $maxsize = 1048576 * $configMaxsize;
        }

        return $maxsize;
    }
}
