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

class OrderStatus 
{
  protected $clientHelper;

  public function __construct(
    ConnectAuthCapture $clientHelper
  ) {
    $this->clientHelper = $clientHelper;
  }

  public function execute(\Magento\Framework\Event\Observer $observer)
  {
    $orderId = $observer->getData('order_id');
    $newAmount = $observer->getData('new_amount');
    $this->clientHelper->setNewAmount($newAmount);
    $this->clientHelper->setTxnByOrderId($orderId);
    $this->clientHelper->send();
  }
}