<?php 
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