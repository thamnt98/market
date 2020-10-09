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

namespace Trans\IntegrationCustomer\Api\Data;

/**
 * @api
 */
interface IntegrationCdbInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const MAGENTO_CUSTOMER_ID = 'magento_customer_id';
    const VERIFIED_EMAIL = 'verified_email';
    const EMAIL = 'email';
    const PHONE_NUMBER = 'telephone';
    const FIRSTNAME = 'firstname';
    const LASTNAME = 'lastname';
    const ID_CARD = 'id_card';
    const GENDER = 'gender';
    const DOB = 'date_of_birth';
    const MARITAL_STATUS = 'marital_status';
    const JOB_STATUS = 'job_status';
    const PASSWORD = 'password';
    const PROFILE_PICTURE_URL = 'profile_picture_url';
    const PROFILE_PICTURE_THUMBNAIL_URL = 'profile_picture_thumbnail_url';
    const GOOGLE_ID = 'google_id';
    const FACEBOOK_ID = 'facebook_id';
    const APPLE_ID = 'apple_id';

    /**
     * CDB gender option values
     */
    const CDB_GENDER_MALE = 'M';
    const CDB_GENDER_FEMALE = 'F';


    //Fix swagger error.
    /**
     * Get data array
     *
     * @return mixed[]
     */
    public function getData();

    /**
     * Get magento customer id
     *
     * @return int
     */
    public function getMagentoCustomerId();

    /**
     * Set magento customer id
     *
     * @param string $customerId
     * @return void
     */
    public function setMagentoCustomerId($customerId);

    /**
     * Get verified email
     *
     * @return string
     */
    public function getVerifiedEmail();

    /**
     * Set verified
     *
     * @param string $verified
     * @return void
     */
    public function setVerifiedEmail($verified);

    /**
     * Get Email
     *
     * @return string
     */
    public function getEmail();

    /**
     * Set Email
     *
     * @param int $email
     * @return void
     */
    public function setEmail($email);

    /**
     * Get Firstname
     *
     * @return string
     */
    public function getFirstname();

    /**
     * Set Firstname
     *
     * @param string $firstname
     * @return void
     */
    public function setFirstname($firstname);

    /**
     * Get Lastname
     *
     * @return string
     */
    public function getLastname();

    /**
     * Set Lastname
     *
     * @param string $lastname
     * @return void
     */
    public function setLastname($lastname);

    /**
     * Get Telephone
     *
     * @return string
     */
    public function getTelephone();

    /**
     * Set Telephone
     *
     * @param string $phone
     * @return void
     */
    public function setTelephone($phone);

    /**
     * Get ID Card
     *
     * @return string
     */
    public function getIdCard();

    /**
     * Set ID Card
     *
     * @param string $idCard
     * @return void
     */
    public function setIdCard($idCard);

    /**
     * Get Gender
     *
     * @return string
     */
    public function getGender();

    /**
     * Set Gender
     *
     * @param string $gender
     * @return void
     */
    public function setGender($gender);

    /**
     * Get Date of Birth
     *
     * @return string
     */
    public function getDateOfBirth();

    /**
     * Set Date of Birth
     *
     * @param string $dob
     * @return void
     */
    public function setDateOfBirth($dob);

    /**
     * Get Marital Status
     *
     * @return string
     */
    public function getMaritalStatus();

    /**
     * Set Marital Status
     *
     * @param string $maritalStatus
     * @return void
     */
    public function setMaritalStatus($maritalStatus);

    /**
     * Get Job Status
     *
     * @return string
     */
    public function getJobStatus();

    /**
     * Set Job Status
     *
     * @param int $jobStatus
     * @return void
     */
    public function setJobStatus($jobStatus);

    /**
     * Get Password
     *
     * @return string
     */
    public function getPassword();

    /**
     * Set Password
     *
     * @param int $password
     * @return void
     */
    public function setPassword($password);

    /**
     * Get Profile Picture URL
     *
     * @return string
     */
    public function getProfilePicture();

    /**
     * Set Profile Picture URL
     *
     * @param int $profilePict
     * @return void
     */
    public function setProfilePicture($profilePict);

    /**
     * Get Profile Picture Thumbnail URL
     *
     * @return string
     */
    public function getProfilePictureThumb();

    /**
     * Set Profile Picture Thumbnail URL
     *
     * @param int $profilePict
     * @return void
     */
    public function setProfilePictureThumb($profilePict);

    /**
     * Get Google ID
     *
     * @return string
     */
    public function getGoogleId();

    /**
     * Set Google ID
     *
     * @param int $googleId
     * @return void
     */
    public function setGoogleId($googleId);

    /**
     * Get Facebook ID
     *
     * @return string
     */
    public function getFacebookId();

    /**
     * Set Facebook ID
     *
     * @param int $fbId
     * @return void
     */
    public function setFacebookId($fbId);

    /**
     * Get Apple ID
     *
     * @return string
     */
    public function getAppleId();

    /**
     * Set Apple ID
     *
     * @param int $appleId
     * @return void
     */
    public function setAppleId($appleId);
}
