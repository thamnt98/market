<?php
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Mepay\Model\Payment\Update;

use Trans\Mepay\Helper\Payment\Transaction;

class Authorize
{
  /**
   * @var Transaction
   */
  protected $transaction;

  /**
   * Constructor method
   * @param Transaction $transaction
   */
  public function __construct(
    Transaction $transaction
  ) {
    $this->transaction = $transaction;
  }
}