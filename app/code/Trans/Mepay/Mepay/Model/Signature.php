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
namespace Trans\Mepay\Model;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Trans\Mepay\Model\Config\Config;
use Trans\Mepay\Logger\Logger;

class Signature
{
  /**
   * @var TimezoneInterfaces
   */
  protected $timezone;

  /**
   * @var Config
   */
  protected $config;

  /**
   * @var Logger
   */
  protected $logger;

  /**
   * Constructor
   * @param TimezoneInterface $timezone
   * @param Config            $config
   * @param Logger $logger
   */
  public function __construct(
    TimezoneInterface $timezone,
    Config $config,
    Logger $logger
  ) {
    $this->timezone = $timezone;
    $this->config = $config;
    $this->logger = $logger;
  }

  /**
	 * create validate signature
	 *
	 * @return string
	 */
	public function receivingValidateSignature() {
		$format      = 'Y-m-d H:i:s';
		$timeStamp   = $this->timezone->date(new \DateTime())->format($format);
		$secretKey   = $this->config->getSecretKey();
		$requestBody = hash_hmac('sha256', $timeStamp . "," . $secretKey, true) . md5('hex');

		return $requestBody;
	}

	/**
	 * generate MD5 signature
	 * @param string $string
	 * @return string
	 */
	public function generateMd5Signature($string)
	{
    $headers = explode(';', $string);
    $secretKey = $this->config->getSecretKey();
    $signature = (isset($headers[0]))? $headers[0] : '';
    $timestamp = (isset($headers[1]))? $headers[1] : '';
    return md5($secretKey.$signature.$timestamp);
	}
}
