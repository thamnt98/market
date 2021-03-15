<?php
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author  Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Mepay\Model;

use Trans\Mepay\Api\Data\ResponseInterface;
use Trans\Mepay\Api\Data\ResponseInterfaceFactory;

/**
 * Class Tokenization
 */
class Tokenization implements \Trans\Mepay\Api\TokenizationInterface
{
    /**
     * @var \Trans\Mepay\Logger\LoggerWrite
     */
    protected $logger;

    /**
     * @var \Trans\Mepay\Model\CardSavedToken\Builder
     */
    protected $builder;

    /**
     * @var \Trans\Mepay\Api\Data\ResponseInterfaceFactory
     */
    protected $responseFactory;

    /**
     * Constructor
     *
     * @param \Trans\Mepay\Logger\LoggerWrite $logger
     * @param Builder $builder
     * @param ResponseInterfaceFactory $responseFactory
     */
    public function __construct(
        \Trans\Mepay\Logger\LoggerWrite $logger,
        \Trans\Mepay\Model\CardSavedToken\Builder $builder,
        \Trans\Mepay\Api\Data\ResponseInterfaceFactory $responseFactory
    ){
        $this->logger = $logger;
        $this->builder = $builder;
        $this->responseFactory = $responseFactory;
    }

    /**
     * {{inheritdoc}}
     */
    public function tokenlist(string $paymentCode)
    {
        $this->logger->writeInfo('==== api get token list ====');
        $tokenList = $this->builder->getByMethod($paymentCode);

        $status = false;
        if($tokenList) {
            $status = true;
        }

        return $this->buildResponse($tokenList, $status);
    }

    /**
     * {{inheritdoc}}
     */
    public function savetoken(string $token, string $method)
    {
        $this->logger->writeInfo('==== save token teting ====');
        $this->logger->writeInfo($token);
        $this->builder->setCustomerToken($token, $method);

        return $this->buildResponse('', true);
    }

    /**
     * Build response
     * @param  mixed $tokenList
     * @param  bool $status
     * @return \ResponseInterface
     */
    protected function buildResponse($tokenList = '', bool $status)
    {
        $response = $this->responseFactory->create();
        $status = '200';
        if (!$status) {
           $status = '201';
        }

        $response->setStatus($status);

        if($tokenList) {
            $response->setList($tokenList);
        }
        
        $this->logger->writeInfo('status: ' . $status);
        $this->logger->writeInfo('list: ' . json_encode($tokenList));
        $this->logger->writeInfo('==== end ====');

        return $response;
    }
}