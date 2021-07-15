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

namespace Trans\Mepay\Api;

/**
 * Webhook interface.
 * @api
 */
interface WebhookInterface
{
  /**
   * Validate webhook
   * @param  string $type
   * @param  \Trans\Mepay\Api\Data\InquiryInterface $inquiry
   * @return \Trans\Mepay\Api\Data\ResponseInterface
   */
  public function validate($type, $inquiry);

    /**
   * Received webhook
   * @param  string $type
   * @param  \Trans\Mepay\Api\Data\TransactionInterface $transaction
   * @param  \Trans\Mepay\Api\Data\InquiryInterface $inquiry
   * @param  string $token
   * @return \Trans\Mepay\Api\Data\ResponseInterface
   */
  public function received($type, $transaction, $inquiry, $token = null);

    /**
   * Validate webhook
   * @param  string $type
   * @param  \Trans\Mepay\Api\Data\TransactionInterface $transaction
   * @param  \Trans\Mepay\Api\Data\InquiryInterface $inquiry
   * @param  string $token
   * @return \Trans\Mepay\Api\Data\ResponseInterface
   */
  public function notif($type, $transaction = null, $inquiry = null, $token = null);
}
