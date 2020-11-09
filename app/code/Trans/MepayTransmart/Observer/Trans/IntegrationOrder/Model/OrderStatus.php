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
namespace Trans\MepayTransmart\Observer\Trans\IntegrationOrder\Model;

use Trans\Mepay\Helper\Gateway\Http\Client\ConnectAuthCapture;
use Trans\Mepay\Model\Config\Config;

class OrderStatus 
{
  /**
   * @var ConnectAuthCapture
   */
  protected $clientHelper;

  /**
   * @var Config
   */
  protected $config;

  /**
   * Constructor
   * @param ConnectAuthCapture $clientHelper
   * @param Config             $config
   */
  public function __construct(
    ConnectAuthCapture $clientHelper,
    Config $config
  ) {
    $this->clientHelper = $clientHelper;
    $this->config = $config;
  }

  /**
   * Execute
   * @param  \Magento\Framework\Event\Observer $observer
   * @return void
   */
  public function execute(\Magento\Framework\Event\Observer $observer)
  {
    if ((int) $this->config->getIsAuthCapture()) {
      $orderId = $observer->getData('order_id');
      $newAmount = $observer->getData('new_amount');
      $this->clientHelper->setNewAmount($newAmount);
      $this->clientHelper->setTxnByOrderId($orderId);
      $this->clientHelper->send();
    }
  }
}