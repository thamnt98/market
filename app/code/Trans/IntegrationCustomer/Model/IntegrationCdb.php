<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCustomer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCustomer\Model;

use \Trans\IntegrationCustomer\Api\Data\IntegrationCdbInterface;

/**
 * CDB integration data model.
 */
class IntegrationCdb extends \Magento\Framework\Api\AbstractSimpleObject implements IntegrationCdbInterface
{
    /**
     * {{@inheritdoc}}
     */
    public function getData()
    {
        return $this->__toArray();
    }

    /**
     * {{@inheritdoc}}
     */
    public function getMagentoCustomerId()
    {
        return $this->_get(self::MAGENTO_CUSTOMER_ID);
    }

    /**
     * {{@inheritdoc}}
     */
    public function setMagentoCustomerId($customerId)
    {
        $this->setData(self::MAGENTO_CUSTOMER_ID, $customerId);
    }

    /**
     * Get verified email
     *
     * @return string
     */
    public function getVerifiedEmail()
    {
        return $this->_get(self::VERIFIED_EMAIL);
    }

    /**
     * Set verified
     *
     * @param string $verified
     * @return void
     */
    public function setVerifiedEmail($verified)
    {
        $this->setData(self::VERIFIED_EMAIL, $verified);
    }

    /**
     * Get Email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->_get(self::EMAIL);
    }

    /**
     * Set Email
     *
     * @param int $email
     * @return void
     */
    public function setEmail($email)
    {
        $this->setData(self::EMAIL, $email);
    }

    /**
     * Get Firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->_get(self::FIRSTNAME);
    }

    /**
     * Set Firstname
     *
     * @param int $firstname
     * @return void
     */
    public function setFirstname($firstname)
    {
        $this->setData(self::FIRSTNAME, $firstname);
    }

    /**
     * Get Lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->_get(self::LASTNAME);
    }

    /**
     * Set Lastname
     *
     * @param int $lastname
     * @return void
     */
    public function setLastname($lastname)
    {
        $this->setData(self::LASTNAME, $lastname);
    }

    /**
     * Get Telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->_get(self::PHONE_NUMBER);
    }

    /**
     * Set Telephone
     *
     * @param int $phone
     * @return void
     */
    public function setTelephone($phone)
    {
        $this->setData(self::PHONE_NUMBER, $phone);
    }

    /**
     * Get ID Card
     *
     * @return string
     */
    public function getIdCard()
    {
        return $this->_get(self::ID_CARD);
    }

    /**
     * Set ID Card
     *
     * @param string $idCard
     * @return void
     */
    public function setIdCard($idCard)
    {
        $this->setData(self::ID_CARD, $idCard);
    }

    /**
     * Get Gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->_get(self::GENDER);
    }

    /**
     * Set Gender
     *
     * @param int $gender
     * @return void
     */
    public function setGender($gender)
    {
        $this->setData(self::GENDER, $gender);
    }

    /**
     * Get Date of Birth
     *
     * @return string
     */
    public function getDateOfBirth()
    {
        return $this->_get(self::DOB);
    }

    /**
     * Set Date of Birth
     *
     * @param int $dob
     * @return void
     */
    public function setDateOfBirth($dob)
    {
        $this->setData(self::DOB, $dob);
    }

    /**
     * Get Marital Status
     *
     * @return string
     */
    public function getMaritalStatus()
    {
        return $this->_get(self::MARITAL_STATUS);
    }

    /**
     * Set Marital Status
     *
     * @param int $maritalStatus
     * @return void
     */
    public function setMaritalStatus($maritalStatus)
    {
        $this->setData(self::MARITAL_STATUS, $maritalStatus);
    }

    /**
     * Get Job Status
     *
     * @return string
     */
    public function getJobStatus()
    {
        return $this->_get(self::JOB_STATUS);
    }

    /**
     * Set Job Status
     *
     * @param int $jobStatus
     * @return void
     */
    public function setJobStatus($jobStatus)
    {
        $this->setData(self::JOB_STATUS, $jobStatus);
    }

    /**
     * Get Password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->_get(self::PASSWORD);
    }

    /**
     * Set Password
     *
     * @param int $password
     * @return void
     */
    public function setPassword($password)
    {
        $this->setData(self::PASSWORD, $password);
    }

    /**
     * Get Profile Picture URL
     *
     * @return string
     */
    public function getProfilePicture()
    {
        return $this->_get(self::PROFILE_PICTURE_URL);
    }

    /**
     * Set Profile Picture URL
     *
     * @param string $profilePict
     * @return void
     */
    public function setProfilePicture($profilePict)
    {
        $this->setData(self::PROFILE_PICTURE_URL, $profilePict);
    }

    /**
     * Get Profile Picture Thumbnail URL
     *
     * @return string
     */
    public function getProfilePictureThumb()
    {
        return $this->_get(self::PROFILE_PICTURE_THUMBNAIL_URL);
    }

    /**
     * Set Profile Picture Thumbnail URL
     *
     * @param string $profilePict
     * @return void
     */
    public function setProfilePictureThumb($profilePict)
    {
        $this->setData(self::PROFILE_PICTURE_THUMBNAIL_URL, $profilePict);
    }

    /**
     * Get Google ID
     *
     * @return string
     */
    public function getGoogleId()
    {
        return $this->_get(self::GOOGLE_ID);
    }

    /**
     * Set Google ID
     *
     * @param int $googleId
     * @return void
     */
    public function setGoogleId($googleId)
    {
        $this->setData(self::GOOGLE_ID, $googleId);
    }

    /**
     * Get Facebook ID
     *
     * @return string
     */
    public function getFacebookId()
    {
        return $this->_get(self::FACEBOOK_ID);
    }

    /**
     * Set Facebook ID
     *
     * @param int $fbId
     * @return void
     */
    public function setFacebookId($fbId)
    {
        $this->setData(self::FACEBOOK_ID, $fbId);
    }

    /**
     * Get Apple ID
     *
     * @return string
     */
    public function getAppleId()
    {
        return $this->_get(self::APPLE_ID);
    }

    /**
     * Set Apple ID
     *
     * @param int $appleId
     * @return void
     */
    public function setAppleId($appleId)
    {
        $this->setData(self::APPLE_ID, $appleId);
    }
}
