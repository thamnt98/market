<?php 
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author  Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Mepay\Api\Data;

interface InquiryCustomerInterface 
{
  /**
   * @var  string
   */
  const NAME = 'name';

  /**
   * @var  string
   */
  const EMAIL = 'email';

  /**
   * @var  string
   */
  const PHONE_NUMBER = 'phoneNumber';

  /**
   * @var  string
   */
  const COUNTRY = 'country';

  /**
   * @var  string
   */
  const POSTAL_CODE = 'postalCode';

  /**
   * Get name
   * @return string
   */
  public function getName();

  /**
   * Set name
   * @param string
   * @return  void]
   */
  public function setName($data);

  /**
   * Get email
   * @return string
   */
  public function getEmail();

  /**
   * Set email
   * @param string
   * @return  void
   */
  public function setEmail($data);

  /**
   * Get phone number
   * @return string
   */
  public function getPhoneNumber();

/**
 * Set phone number
 * @param string
 * @return  void
 */
  public function setPhoneNumber($data);

  /**
   * Get country
   * @return string
   */
  public function getCountry();

  /**
   * Set country
   * @param string
   * @return  void
   */
  public function setCountry($data);

  /**
   * Get postal code
   * @return string
   */
  public function getPostalCode();

  /**
   * Set postal code
   * @param string
   * @return  void
   */
  public function setPostalCode($data);
}