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
namespace Trans\Mepay\Gateway\Http;

use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Trans\Mepay\Model\Config\Config;
use Trans\Mepay\Logger\Logger;

class TransferFactory extends AbstractTransfer implements TransferFactoryInterface
{
    /**
     * @var TransferBuilder
     */
    private $transferBuilder;

    /**
     * Constructor
     * @param TransferBuilder $transferBuilder
     * @param Config          $config
     * @param Logger          $logger
     */
    public function __construct(
      TransferBuilder $transferBuilder,
      Config $config,
      Logger $logger
    ) {
        $this->transferBuilder = $transferBuilder;
        parent::__construct($config, $logger);
    }

    /**
     * @param array $body
     * @return \Magento\Payment\Gateway\Http\TransferInterface
     */
    public function create(array $body)
    {
        return $this->transferBuilder
          ->setUri($this->getUri())
          ->setHeaders($this->getPostHeaders())
          ->setMethod($this->getPostMethod())
          ->setBody($body)
          ->setClientConfig(['isRaw' => true])
          ->build();
    }

}
