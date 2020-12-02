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
namespace Trans\Mepay\Model\Converter;

use Magento\Framework\Api\SimpleDataObjectConverter;

class SnakeCaseConverter
{
  public function execute($dataObject, $outputData)
  {
    foreach ($outputData as $key => $value) {
      unset($outputData[$key]);
      if ($value) {
        $key = SimpleDataObjectConverter::snakeCaseToCamelCase($key);
        $outputData[$key]=$value;
      }
    }
    return $outputData;
  }
}