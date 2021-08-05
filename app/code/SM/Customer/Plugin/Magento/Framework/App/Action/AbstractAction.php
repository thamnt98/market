<?php
/**
 * SM\MagePalGTMExtension\Plugin\Magento\Framework\App\Action
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */
namespace SM\Customer\Plugin\Magento\Framework\App\Action;

use Closure;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\RequestInterface;

/**
 * Class AbstractAction
 * @package SM\Customer\Plugin\Magento\Framework\App\Action
 */
class AbstractAction
{
    const KEY_SESSION_CUSTOMER_ID = "session_customer_id";

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var Context
     */
    protected $httpContext;
    /**
     * @param Session $customerSession
     * @param Context $httpContext
     */
    public function __construct(
        Session $customerSession,
        Context $httpContext
    ) {
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
    }

    /**
     * @param ActionInterface $subject
     * @param Closure $proceed
     * @param RequestInterface $request
     * @return mixed
     */
    public function aroundDispatch(
        ActionInterface $subject,
        Closure $proceed,
        RequestInterface $request
    ) {

        /**
         * Can not get customer_id in a cached block, so I will set the customer_id
         * before Controller dispatched.
         */
        if ($this->customerSession->isLoggedIn() &&
            $this->customerSession->getCustomer()) {
            $this->httpContext->setValue(
                self::KEY_SESSION_CUSTOMER_ID,
                $this->customerSession->getCustomer()->getId(),
                0
            );
        }

        return $proceed($request);
    }
}
