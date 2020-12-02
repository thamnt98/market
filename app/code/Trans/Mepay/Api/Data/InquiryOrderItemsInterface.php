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

interface InquiryOrderItemsInterface 
{
  /**
   * @var  string
   */
  const NAME = 'name';

  /**
   * @var  string
   */
  const QUANTITY = 'quantity';

  /**
   * @var  string
   */
  const AMOUNT = 'amount';

  /**
   * Get name
   * @return string
   */
  public function getName();

  /**
   * Set name
   * @param string $data
   * @return  void
   */
  public function setName($data);

  /**
   * Get quantity
   * @return string
   */
  public function getQuantity();

  /**
   * Set quantity
   * @param string $data
   * @return  void
   */
  public function setQuantity($data);

  /**
   * Get amount
   * @return string
   */
  public function getAmount();

  /**
   * Set amount
   * @param string $data
   * @return  void
   */
  public function setAmount($data);
}