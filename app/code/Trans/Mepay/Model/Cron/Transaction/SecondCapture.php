<?php 
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2021 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Mepay\Model\Cron\Transaction;

use Trans\Mepay\Helper\Gateway\Http\Client\ConnectAuthCapture;
use Trans\Mepay\Logger\Logger;

class SecondCapture
{
    /**
     * @var \Trans\Mepay\Helper\Gateway\Http\Client\ConnectAuthCapture
     */
    protected $authCaptureHelper;

    /**
     * @var \Trans\Mepay\Logger\Logger
     */
    protected $logger;

    /**
     * Construct
     * @param ConnectAuthCapture $authCaptureHelper
     * @param Logger $logger
     */
    public function __construct(ConnectAuthCapture $authCaptureHelper, Logger $logger)
    {
        $this->authCaptureHelper = $authCaptureHelper;
        $this->logger = $logger;
    }

    /**
     * CaptureRequest
     * @param  string $reffNumber
     * @param  int $amountAdjustment
     * @return Array
     */
    public function captureRequest($reffNumber, $amountAdjustment)
    {
        try {
            $this->authCaptureHelper->setReferenceNumber($reffNumber);
            $this->authCaptureHelper->setAdjustmentAmount($amountAdjustment);
            $this->authCaptureHelper->setTxnByOrderId($reffNumber);
            $send = $this->authCaptureHelper->send();
        } catch (\Exception $e) {
            $send = $e;
        }

        if (is_array($send)) {
            $send = array_pop($send);
            $send = json_decode($send, true);
        } else {
            $send = ['error' => [
                'statusCode' => '401', 
                'name'=> 'Magento Internal Error', 
                'message' => $send->getMessage()
            ]];
        }
        return $send;
    }
}