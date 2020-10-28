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
namespace Trans\Mepay\Helper\Response;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;

class Response extends AbstractHelper
{
  /**
   * @var string
   */
  const RESPONSE_ID = 'id';

  /**
   * @var string
   */
  const RESPONSE_ERROR = 'error';

  /**
   * @var Json
   */
  protected $json;

  /**
   * Constructor
   * @param Context $context
   */
  public function __construct(
    Context $context,
    Json $json
  ) {
    $this->json = $json;
    parent::__construct($context);
  }

  /**
   * Extract response into single dimensional array
   * @param  array  $resp
   * @return array
   */
  public function extract($resp)
  {
    $firstResult = [];
    $secondResult = [];
    $thirdResult = [];
    foreach ($resp as $key => $value) {
      if(is_array($value)) {
        $secondResult = $this->extract($value);
      } elseif(is_object($value)) {
        $value = $value->getData();
        $thirdResult = $this->extract($value);
      }else {
        $firstResult[$key] = $value;
      }
    }
    return array_merge($firstResult, $secondResult);
  }

  /**
   * unserialize response
   * @param  array  $response
   * @return array
   */
  public function unserialize(array $response)
  {
    foreach ($response as $key => $value) {
      return $this->json->unserialize($value);
    }
  }
}
