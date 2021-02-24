<?php
/**
 * @category Trans
 * @package  Trans_MgPayment
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
 namespace Trans\Mepay\Model\Config\Source;

 use Magento\Framework\Option\ArrayInterface;
 use Trans\Mepay\Model\Config\Provider\Cc;
 use Trans\Mepay\Model\Config\Provider\Debit;
 use Trans\Mepay\Model\Config\Provider\Va;
 use Trans\Mepay\Model\Config\Provider\Qris;

 /**
  * Class \Trans\Mepay\Model\Config\Source\Environment
  */
 class Provider implements ArrayInterface
 {
   /**
    * @var string
    */
  const MEGA_CC = 'megacc';

  /**
    * @var string
    */
  const MEGA_DEBIT = 'megadc';

  /**
   * @var string
   */
  const LABEL_MEGA_CC = 'Bank Mega Credit/debit card';

  /**
   * @var string
   */
  const LABEL_MEGA_DEBIT = 'Bank Mega Debit';

  /**
   * @var string
   */
  const MEGA_VA = 'megava';

  /**
   * @var string
   */
  const LABEL_MEGA_VA = 'Bank Mega Virtual Account';

  /**
   * @var string
   */
  const MEGA_QRIS = 'megaqris';

  /**
   * @var string
   */
  const LABEL_MEGA_QRIS = 'Bank Mega QRIS';

   /**
    * Possible provider types
    *
    * @return array
    */
   public function toOptionArray()
   {
     return [
       [
         'value' => self::MEGA_CC,
         'label' => self::LABEL_MEGA_CC
       ],
       [
         'value' => self::MEGA_DEBIT,
         'label' => self::LABEL_MEGA_DEBIT
       ],
       [
         'value' => self::MEGA_VA,
         'label' => self::LABEL_MEGA_VA
       ],
       [
         'value' => self::MEGA_QRIS,
         'label' => self::LABEL_MEGA_QRIS
       ]
     ];
   }

   /**
    * Get Payment Source
    * @param  string $paymentCode
    * @return string
    */
   public function getPaymentSource($paymentCode = '')
   {
    $source = '';
    switch($paymentCode) {
      case Cc::CODE_CC : $source = self::MEGA_CC;
        break;
      case Debit::CODE : $source = self::MEGA_DEBIT;
        break;
      case Va::CODE_VA : $source = self::MEGA_VA;
        break;
      case Qris::CODE_QRIS : $source = self::MEGA_QRIS;
        break;
    }
    return $source;
   }
 }
