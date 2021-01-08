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
namespace Trans\Mepay\Gateway\Http\Client;

use Magento\Framework\HTTP\ZendClient;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\ConverterInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Trans\Mepay\Model\Config\Config;
use Trans\Mepay\Gateway\Request\PaymentSourceMethodDataBuilder;
use Trans\Mepay\Logger\LoggerWrite;


class Connect implements ClientInterface
{
  /**
   * @var ZendClientFactory
   */
  private $clientFactory;

  /**
   * @var ConverterInterface | null
   */
  private $converter;

  /**
   * @var Logger
   */
  private $logger;

  /**
   * @var Json
   */
  private $json;

  /**
   * @var Config
   */
  private $config;

  /**
   * @param ZendClientFactory $clientFactory
   * @param Logger $logger
   * @param ConverterInterface | null $converter
   */
  public function __construct(
      ZendClientFactory $clientFactory,
      LoggerWrite $logger,
      Config $config,
      Json $json,
      ConverterInterface $converter = null
  ) {
      $this->clientFactory = $clientFactory;
      $this->converter = $converter;
      $this->logger = $logger;
      $this->json = $json;
      $this->config = $config;
  }

  /**
   * {inheritdoc}
   */
  public function placeRequest(TransferInterface $transferObject)
  {
      $log = [
          'request' => $transferObject->getBody(),
          'request_uri' => $transferObject->getUri()
      ];
      
      $this->logger->loggingGatewayHttpClientConnect('placeRequest',['log' => $log]);

      $result = [];

      /** @var ZendClient $client */
      $client = $this->clientFactory->create();
      $client->setMethod($transferObject->getMethod());
      $clientConfig = $transferObject->getClientConfig();

      switch ($transferObject->getMethod()) {
          case \Zend_Http_Client::GET:
              $client->setParameterGet($transferObject->getBody());
              break;
          case \Zend_Http_Client::POST:
              if (isset($clientConfig['isRaw']) && $clientConfig['isRaw']) {
                $client->setRawData($this->json->serialize($transferObject->getBody()));
                unset($clientConfig['isRaw']);
              } else {
                $client->setParameterPost($transferObject->getBody());
              }
              break;
          default:
              $message = sprintf(
                  'Unsupported HTTP method %s',
                  $transferObject->getMethod()
              );
              $this->logger->loggingGatewayHttpClientConnect('placeRequestError',['message'=>$message]);
              throw new \LogicException($message);
      }

      $client->setConfig($clientConfig);
      $client->setHeaders($transferObject->getHeaders());
      $client->setUri($transferObject->getUri());

      try {
          $response = $client->request();
          $responseBody = $this->json->unserialize($response->getBody());
          $preAuthData = $this->getPreAuthData($transferObject->getBody());
          if (empty($preAuthData) == false) {
              $responseBody = \array_merge($responseBody, $preAuthData);
          }
          $responseBody = $this->json->serialize($responseBody);
          $result = $this->converter ? $this->converter->convert($responseBody) : [$responseBody];
          $log['response'] = $result;
      } catch (\Zend_Http_Client_Exception $e) {

          $this->logger->loggingGatewayHttpClientConnect('placeRequestError',['message'=>$e->getMessage()]);
          throw new \Magento\Payment\Gateway\Http\ClientException(
              __($e->getMessage())
          );
      } catch (\Magento\Payment\Gateway\Http\ConverterException $e) {

          $this->logger->loggingGatewayHttpClientConnect('placeRequestError',['message'=>$e->getMessage()]);
          throw $e;
      } finally {

          $this->logger->log($this->json->serialize($log));
          $this->logger->loggingGatewayHttpClientConnect('placeRequest',['log'=>$log,'client'=>$client]);
      }

      return $result;
  }

  /**
   * Get pre-auth data
   *
   * @param array $request
   * @return array
   */
  protected function getPreAuthData($request)
  {
    if ($this->isPreAuth($request)) {
        return [PaymentSourceMethodDataBuilder::PAYMENT_SOURCE_METHOD => PaymentSourceMethodDataBuilder::AUTH_CAPTURE];
    }
    return [];
  }

  /**
   * Is pre-auth
   *
   * @param array $request
   * @return boolean
   */
  protected function isPreAuth($request)
  {
        if (isset($request[PaymentSourceMethodDataBuilder::PAYMENT_SOURCE_METHOD])
            && $request[PaymentSourceMethodDataBuilder::PAYMENT_SOURCE_METHOD] == PaymentSourceMethodDataBuilder::AUTH_CAPTURE
        ) {
            return true;
        }
      return false;
  }

}
