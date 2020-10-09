<?php

namespace SM\Checkout\Plugin\Checkout\Controller;

/**
 * Class CartPlugin
 * @package SM\Checkout\Plugin\Checkout\Controller
 */
class CartPlugin
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * CartPlugin constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Checkout\Controller\Cart $subject
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function beforeDispatch(
        \Magento\Checkout\Controller\Cart $subject,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->checkoutSession->unsPreShippingType();
        $this->checkoutSession->unsPreAddress();
    }
}
