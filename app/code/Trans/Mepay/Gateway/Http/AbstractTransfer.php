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
namespace Trans\Mepay\Gateway\Http;

use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Trans\Mepay\Model\Config\Config;
use Trans\Mepay\Logger\Logger;

class AbstractTransfer
{
  /**
   * @var string
   */
  const HOST = 'Host';

  /**
   * @var string
   */
  const AUTHORIZATION = 'Authorization';

  /**
   * @var string
   */
  const CONTENT_TYPE = 'Content-Type';

  /**
   * @var string
   */
  const METHOD_POST = 'POST';

  /**
   * @var  string
   */
  const METHOD_GET = 'GET';

  /**
   * @var \Config
   */
  protected $config;

  /**
   * @var Logger
   */
  protected $logger;

  /**
   * Constructor
   * @param Config $config
   * @param Logger $logger
   */
  public function __construct(
    Config $config,
    Logger $logger
  ) {
    $this->config = $config;
    $this->logger = $logger;
  }

  /**
   * Get post headers
   * @return []
   */
  public function getPostHeaders()
  {
    return [
      self::AUTHORIZATION => $this->config->getApiKey(),
      self::CONTENT_TYPE => $this->config->getContentType()
    ];
  }

  /**
   * Get Post Method
   * @return string
   */
  public function getPostMethod()
  {
    return self::METHOD_POST;
  }

  public function getGetMethod()
  {
    return self::METHOD_GET;
  }

  /**
   * Get Url
   * @return string
   */
  public function getUri()
  {
    return $this->config->getEndpointUri();
  }

}
