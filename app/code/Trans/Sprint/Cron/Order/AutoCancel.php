<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Cron\Order;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * class AutoCancel
 */
class AutoCancel 
{
	/**
     * @var \Magento\Payment\Model\Config
     */
    protected $paymentConfig;

    /**
     * @var \Trans\Sprint\Api\AutoCancelInterface
     */
    protected $autoCancel;

    /**
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param \Trans\Sprint\Api\AutoCancelInterface $autoCancel
     */
    public function __construct(
        \Magento\Payment\Model\Config $paymentConfig,
        \Trans\Sprint\Api\AutoCancelInterface $autoCancel
    ) {
        $this->paymentConfig = $paymentConfig;
        $this->autoCancel = $autoCancel;

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/auto_cancel.log');
        $logger = new \Zend\Log\Logger();
        $this->logger = $logger->addWriter($writer);
    }

    /**
     * Write to system.log
     *
     * @return void
     */
    public function execute() {
        $class = get_class();
        try {
            $payments = $this->getPayments();
            $this->autoCancel->cancelExpiredOrder($payments);
        } catch (\Exception $ex) {
            $this->logger->info("<=err".$class." ".$ex->getMessage());
        }
        $this->logger->info("<=End".$class );
    }

    /**
     * get payments code
     *
     * @return array
     */
    protected function getPayments()
    {
        $payments = $this->paymentConfig->getActiveMethods();
        $methods = array();
        
        foreach ($payments as $paymentCode => $paymentModel) {
            if (strpos($paymentCode, '_va') !== false) {
                $methods[] = $paymentCode;
            }
        }

        return $methods;
    }
}
