<?php 
/**
 * @category Trans
 * @package  Trans_MepayTransmart
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\MepayTransmart\Helper;

use SM\Checkout\Helper\OrderReferenceNumber;

class TransmartOrderReferenceNumber extends OrderReferenceNumber
{
    /**
     * @inheritdoc
     */
    public function generateReferenceNumber($order, $isSprint = true)
    {
        if ($isSprint === false)
          return $this->generateReferenceNumberNonSprint($order);

        $prefix = "";
        if ($this->isDigitalProduct($order)) {
            $prefix = "DP-";
        }
        $device = 'Web-';
        if ($this->isMobile()) {
            $device = 'App-';
        }
        $incrementId = $order->getIncrementId();
        return $prefix.$device.$incrementId;
    }

    /**
     * Generate order reference number non sprint
     * @param \Magento\Payment\Gateway\Order\OrderAdapter $order
     * @return string
     */
    public function generateReferenceNumberNonSprint($order)
    {
      $incrementId = $order->getOrderIncrementId();
      $incrementId = explode('-', $incrementId);
      $prefix = "";
      $device = "Web-";
      if ($this->isMobile()) {
            $device = 'App-';
        }
      return $prefix.$device.$incrementId[0];
    }
}