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
use Magento\Framework\Event\ObserverInterface;

class OrderStatus implements ObserverInterface
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
   * @var \Trans\Mepay\Logger\LoggerWrite
   */
  protected $logger;

  /**
   * Constructor
   * @param ConnectAuthCapture $clientHelper
   * @param Config $config
   * @param \Trans\Mepay\Logger\LoggerWrite $loggetWrite
   */
  public function __construct(
    ConnectAuthCapture $clientHelper,
    Config $config,
    \Trans\Mepay\Logger\Logger $logger
  ) {
    $this->clientHelper = $clientHelper;
    $this->config = $config;
    $this->logger = $logger;

    // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/trans_mepay.log');
    // $logger = new \Zend\Log\Logger();
    // $this->logger = $logger->addWriter($writer);
  }

  /**
   * Execute
   * @param  \Magento\Framework\Event\Observer $observer
   * @return void
   */
  public function execute(\Magento\Framework\Event\Observer $observer)
  {
    if ((int) $this->config->getIsAuthCapture()) {
      $this->logger->info('== {{Auth Capture Start}} ==');

      $orderId = $observer->getData('order_id');
      $newAmount = $observer->getData('new_amount');
      $amount = $observer->getData('amount');
      $this->logger->info('Data order_id = ' . $orderId);
      $this->logger->info('Data new_amount = ' . $newAmount);
      $this->logger->info('Data amount = ' . $amount);

      $this->clientHelper->setAmount($amount);
      $this->clientHelper->setNewAmount($newAmount);
      $this->clientHelper->setTxnByOrderId($orderId);
      $this->clientHelper->send();
      
      $this->logger->info('== {{Auth Capture End}} ==');
    }
  }
}