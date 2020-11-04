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

 /**
  * Class \Trans\MgPayment\Model\Config\Source\Environment
  */
 class Environment implements ArrayInterface
 {
   /**
    * @var string
    */
  const ENVIRONMENT_PRODUCTION = 'production';

  /**
   * @var string
   */
  const LABEL_ENVIRONMENT_PRODUCTION = 'Production';

  /**
   * @var string
   */
  const ENVIRONMENT_DEVELOPMENT = 'development';

  /**
   * @var string
   */
  const LABEL_ENVIRONMENT_DEVELOPMENT = 'Development';

   /**
    * Possible environment types
    *
    * @return array
    */
   public function toOptionArray()
   {
     return [
       [
         'value' => self::ENVIRONMENT_DEVELOPMENT,
         'label' => self::LABEL_ENVIRONMENT_DEVELOPMENT
       ],
       [
         'value' => self::ENVIRONMENT_PRODUCTION,
         'label' => self::LABEL_ENVIRONMENT_PRODUCTION
       ]
     ];
   }
 }
