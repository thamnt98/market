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
namespace Trans\Mepay\Plugin\Magento\Webapi\Controller;

use Magento\Webapi\Controller\Rest;
use Magento\Framework\App\RequestInterface;
use Magento\Authorization\Model\CompositeUserContext;
use Trans\Mepay\Observer\Magento\Framework\AppInterface\AutoRecoverCartForPendingPayment;
use Trans\Mepay\Logger\LoggerWrite;

class RestPlugin
{
    /**
     * @var \Magento\Authorization\Model\CompositeUserContext
     */
    protected $userContext;

    /**
     * @var \Trans\Mepay\Observer\Magento\Framework\AppInterface\AutoRecoverCartForPendingPayment
     */
    protected $autoRecover;

    /**
     * @var \Trans\Mepay\Logger\LoggerWrite
     */
    protected $logger;

    /**
     * Constructor
     * @param CompositeUserContext $userContext
     * @param AutoRecoverCartForPendingPayment $autoRecover
     * @param LoggerWrite $logger
     */
    public function __construct(
        CompositeUserContext $userContext,
        AutoRecoverCartForPendingPayment $autoRecover,
        LoggerWrite $logger
    ) {
        $this->userContext = $userContext;
        $this->orderHelper = $orderHelper;
        $this->autoRecover = $autoRecover;
        $this->logger = $logger;
    }

    /**
     * Before Dispatch
     * @param  \Magento\Webapi\Controller\Rest $subject
     * @param  \Magento\Framework\App\RequestInterface $request
     * @return Magento\Framework\App\RequestInterface[]
     */
    public function beforeDispatch(Rest $subject, RequestInterface $request)
    {
        $customerId = $this->userContext->getUserId();
        if ($customerId) {
            $order = $this->autoRecover->getLastCustomerOrder($customerId);
            $this->autoRecover->doCancelationOrderAndRestoreCart($order);
        }
        return[$request];
    }
}
