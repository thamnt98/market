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
namespace Trans\Mepay\Logger\Write\Gateway\Http\Client;

class Connect 
{
  /**
   * Logging place request
   * @param  $logger
   * @param  $log
   * @param  $client
   * @return void
   */
  public function logPlaceRequest($logger, $request, $response, $client)
  {
    $logger->debug(" ================= Request Builder ===============");
    $logger->debug(" Content-Type: ".$client->getHeader('Content-Type'));
    $logger->debug(" Authorization: ".$client->getHeader('Authorization'));
    $logger->debug(" ----");
    $logger->debug(" Body Request:".$request);
    $logger->debug(" ----");
    $logger->debug(" Response: ".$response);
  }

  /**
   * Log error
   * @param  $logger
   * @param  $message
   * @return void
   */
  public function logError($logger, $message)
  {
    $logger->debug('========= Request Failed =======');
    $logger->debug($message);
  }
}