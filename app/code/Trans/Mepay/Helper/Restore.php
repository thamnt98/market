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
namespace Trans\Mepay\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\Request\Http;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\AdvancedCheckout\Model\CartFactory;
use Magento\Checkout\Model\Cart as CartModel;
use Magento\Checkout\Helper\Cart;
use Magento\Checkout\Model\Session;

class Restore extends AbstractHelper
{
    /**
     * @var array
     */
    const APPLICABLE_MODULES = [
        'checkout'
    ];

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $http;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepo;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $cartHelper;

    /**
     * Constructor
     * @param Context $context
     * @param RequestInterface $request
     * @param HttpContext $httpContext
     * @param Http $http
     * @param CartRepositoryInterface quoteRepo
     * @param CartFactory cart
     * @param CartHelper cartHelper
     * @param ProductRepositoryInterface productRepo
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        HttpContext $httpContext,
        Http $http,
        CartRepositoryInterface $quoteRepo,
        Cart $cartHelper,
        Session $session
    ) {
        $this->request = $request;
        $this->httpContext = $httpContext;
        $this->http = $http;
        $this->quoteRepo = $quoteRepo;
        $this->cartHelper = $cartHelper;
        $this->session = $session;
        parent::__construct($context);
    }

    /**
     * Is request valid for auto cancel order & auto recover cart
     * @return boolean
     */
    public function isValid()
    {
        if($this->isAjaxRequest())
            return false;
        if($this->isLogin() == false)
            return false;
        if($this->isModuleApplicable() == false)
            return false;
        return true;
    }

    /**
     * Is ajax request
     * @return boolean
     */
    public function isAjaxRequest()
    {
        return $this->request->isXmlHttpRequest();
    }

    /**
     * Is login
     * @return boolean [description]
     */
    public function isLogin()
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    /**
     * Is module applicable
     * @return boolean
     */
    public function isModuleApplicable()
    {
        return \in_array($this->http->getModuleName(), self::APPLICABLE_MODULES);
    }

    /**
     * Customer has active cart
     * @param  int $customerId
     * @return boolean
     */
    public function hasActiveCart(int $customerId)
    {
        return ($this->cartHelper->getItemsCount())? true : false;
    }

    /**
     * Restore customer cart
     * @param  int $quoteId
     * @return \Magento\Quote\Model\Quote
     */
    public function restoreQuote(int $quoteId)
    {
        $quote = $this->quoteRepo->get($quoteId);
        return $this->session->restoreQuote();
        //return $this->cartModel->copyQuote($quote, true);
        //return $this->cart->create()->copyQuote($quote, true);
    }

}

