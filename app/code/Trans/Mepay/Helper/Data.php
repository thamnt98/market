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
namespace Trans\Mepay\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Trans\Mepay\Model\Config\Provider\Cc;
use Trans\Mepay\Model\Config\Provider\Qris;
use Trans\Mepay\Model\Config\Provider\Va;

class Data extends AbstractHelper
{
  /**
   * @var array
   */
  const BANK_MEGA_PAYMENT_METHOD = [ Cc::CODE_CC, Qris::CODE_QRIS, Va::CODE_VA ];

  /**
   * Constructor
   * @param Context $context
   */
  public function __construct(Context $context) {parent::__construct($context);}

  /**
   * Get class instance
   * @param  string $strClass
   * @return Class
   */
  public static function getClassInstance($strClass)
  {
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    return $objectManager->create($strClass);
  }

  /**
   * Get connection class
   * @return \Magento\Framework\App\ResourceConnection
   */
  public static function getConnectionClass()
  {
    return self::getClassInstance('Magento\Framework\App\ResourceConnection');
  }

  /**
   * Get connection
   * @return connection
   */
  public static function getConnection()
  {
    return self::getConnectionClass()->getConnection();
  }

  /**
   * Is payment method is bank mega
   * @param  string  $method
   * @return boolean
   */
  public static function isMegaMethod($method)
  {
    return in_array($method, self::BANK_MEGA_PAYMENT_METHOD);
  }
}