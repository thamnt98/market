<?php 
namespace Trans\MepayTransmart\Helper;

use SM\Checkout\Helper\OrderReferenceNumber;

class TransmartOrderReferenceNumber extends OrderReferenceNumber
{
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