<?php 
namespace Trans\MepayTransmart\Block\Checkout;

use SM\Checkout\Block\Checkout\Success;

class TransmartSuccess extends Success
{
  public function isSucceed()
    {
        $paymentMethod = $this->getPaymentMethod();
        if ($paymentMethod == 'trans_mepay_va' || $paymentMethod == 'trans_mepay_qris' || $paymentMethod == 'trans_mepay_cc') {
          return true;
        }
        return $this->paymentHelper->isCredit($paymentMethod) || $this->paymentHelper->isInstallment($paymentMethod) || ($this->paymentHelper->isVirtualAccount($paymentMethod) && $this->isPaid());
    }
}