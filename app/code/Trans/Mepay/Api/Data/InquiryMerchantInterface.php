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

interface InquiryMerchantInterface 
{
  /**
   * string
   */
  const ID = 'id';

  /**
   * string
   */
  const NAME = 'name';

  /**
   * string
   */
  const STATUS = 'status';

  /**
   * string
   */
  const PARTNER_ID = 'partnerId';

  /**
   * Get id
   * @return string
   */
  public function getId();

  /**
   * Set id
   * @param string
   * @return  void
   */
  public function setId($data);

  /**
   * Get name
   * @return string
   */
  public function getName();

  /**
   * Set name
   * @param string
   * @return  void
   */
  public function setName($data);

  /**
   * Get status
   * @return string
   */
  public function getStatus();

  /**
   * Set status
   * @param string
   * @return  void
   */
  public function setStatus($data);

  /**
   * Get partner id
   * @return string
   */
  public function getPartnerId();

  /**
   * Set partner id
   * @param string
   * @return  void
   */
  public function setPartnerId($data);
}