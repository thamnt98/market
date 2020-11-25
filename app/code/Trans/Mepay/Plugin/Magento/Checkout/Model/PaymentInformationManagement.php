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
namespace Trans\Mepay\Plugin\Magento\Checkout\Model;

use Trans\Mepay\Helper\Data;

class PaymentInformationManagement
{
  /**
   * After savePaymentInformationPlaceOrder
   * @param  \Magento\Checkout\Model\PaymentInformationManagement $subject
   * @param  int $result
   * @return int
   */
  public function afterSavePaymentInformationAndPlaceOrder(
    \Magento\Checkout\Model\PaymentInformationManagement $subject,
    $result
  ){
    return $result;
  }
}