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
namespace Trans\Mepay\Logger;

use Magento\Framework\Serialize\Serializer\Json;
use Trans\Mepay\Logger\Write\Gateway\Http\Client\Connect;
use Trans\Mepay\Logger\Write\Model\Webhook;
use Trans\Mepay\Model\Config\Config;

class LoggerWrite
{
  protected $json;
  protected $logger;
  protected $config;
  protected $loggerGatewayHttpClientConnect;
  protected $loggerModelWebhook;
  public function __construct(
    Json $json,
    Logger $logger,
    Config $config,
    Connect $loggerGatewayHttpClientConnect,
    Webhook $loggerModelWebhook
  ) {
    $this->json = $json;
    $this->logger = $logger;
    $this->config = $config;
    $this->loggerGatewayHttpClientConnect = $loggerGatewayHttpClientConnect;
    $this->loggerModelWebhook = $loggerModelWebhook;
  }

  public function loggingGatewayHttpClientConnect($methodName, $param = [])
  {
    if ($this->config->isDebug()) {
      switch ($methodName) {
        case 'placeRequest': $this->loggerGatewayHttpClientConnect->logPlaceRequest(
          $this->logger, 
          $this->json->serialize($param['log']['request']), 
          $param['log']['response'][0],
          $param['client']
        );
          break;
        case 'placeRequestError' : $this->loggerGatewayHttpClientConnect->logError($this->logger, $param['message']);
          break;
      }
    }
  }

  public function loggingModelWebhook($methodName, $param = [])
  {
    if ($this->config->isDebug()) {
      switch ($methodName) {
        case 'notif': 
        $this->loggerModelWebhook->logNotif($this->logger, $param['type'], $param['transaction'], $param['inquiry'], $param['token']);
          break;
        case 'response' : $this->loggerModelWebhook->logResponse($this->logger, $param['type'], $param['status'], $param['response']);
          break;
      }
    }
  }

  public function log(string $message)
  {
    $this->logger->debug('===== Common Log ====');
    $this->logger->debug($message);
  }
}
