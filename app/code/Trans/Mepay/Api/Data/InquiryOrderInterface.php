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

interface InquiryOrderInterface 
{
  /**
   * @var  string
   */
  const ID = 'id';

  /**
   * @var  string
   */
  const ITEMS = 'items';

  /**
   * @var  string
   */
  const DISABLE_PROMO = 'disablePromo';

    /**
   * @var  string
   */
  const AFTER_DISCOUNT = 'disablePromo';

  /**
   * Get id
   * @return string
   */
  public function getId();

  /**
   * Set id
   * @param  string
   * @return void
   */
  public function setId($data);

  /**
   * Get items
   * @return mixed
   */
  public function getItems();

  /**
   * Set items
   * @param mixed
   * @return  void
   */
  public function setItems($data);

  /**
   * Get Disable promo
   * @return boolean
   */
  public function getDisablePromo();

  /**
   * Set Disable promo
   * @param boolean
   * @return  void
   */
  public function setDisablePromo($data);

    /**
   * Get After discount
   * @return boolean
   */
  public function getAfterDiscount();

  /**
   * Set after discount
   * @param boolean
   * @return  void
   */
  public function setAfterDiscount($data);
}