<?php

/**
 * @category Trans
 * @package  Trans_CustomerMyProfile
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\CustomerMyProfile\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 */
class Data extends AbstractHelper
{
    /**
     * General Configuration
     */
    const XML_MAXSIZE_PROFILE_PICTURE = 'customersetting/myprofilesetting/profilepicturemaxsize';
    const XML_DISABLE_DOB_REGISTER = 'customersetting/generalsetting/diabledobregister';
    const XML_DISABLE_GENDER_REGISTER = 'customersetting/generalsetting/diablegenderregister';
    const XML_DOB_CHANGE_LIMIT = 'customersetting/myprofilesetting/dob_change_limit';

    /**
     * General Variables
     */
    const CHANGE_TELEPHONE = 'change_telephone';
    const CHANGE_EMAIL = 'change_email';
    const CHANGE_PASSWORD = 'change_password';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Get configuration max size profile picture
     *
     * @return bool
     */
    public function getMaxsizeProfilePicture()
    {
        return $this->scopeConfig->getValue(
            self::XML_MAXSIZE_PROFILE_PICTURE,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    /**
     * Get configuration disable dob register
     *
     * @return bool
     */
    public function getDisableDobRegister()
    {
        return $this->scopeConfig->getValue(
            self::XML_DISABLE_DOB_REGISTER,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    /**
     * Get configuration disable gender register
     *
     * @return bool
     */
    public function getDisableGenderRegister()
    {
        return $this->scopeConfig->getValue(
            self::XML_DISABLE_GENDER_REGISTER,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDobChangeLimit()
    {
        return $this->scopeConfig->getValue(
            self::XML_DOB_CHANGE_LIMIT,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    /**
     * @param bool $save
     * @return array
     */
    public function ignoreAttribute($save = false)
    {
        if ($save) {
            return ['coachmarks', 'language', 'city', 'district', 'region', 'telephone'];
        }
        return ['coachmarks', 'language', 'city', 'district', 'region', 'telephone', 'marital_status', 'dob_change_number', 'profile_picture', 'is_verified_email', 'is_disabled_dob'];
    }
}
