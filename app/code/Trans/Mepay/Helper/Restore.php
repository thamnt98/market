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
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\App\Request\Http;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Model\Cart as CartModel;
use Magento\Checkout\Helper\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;

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
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    protected $userContext;

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
        UserContextInterface $userContext,
        Http $http,
        CartRepositoryInterface $quoteRepo,
        Cart $cartHelper,
        Session $session
    ) {
        $this->request = $request;
        $this->httpContext = $httpContext;
        $this->userContext = $userContext;
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
        if($this->hasActiveCart())
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
        if ($this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH))
            return true;
        if ($this->session->getLastOrderId())
            return true;
        if ($this->userContext->getUserId())
            return true;
        return false;

    }

    /**
     * Is module applicable
     * @return boolean
     */
    public function isModuleApplicable()
    {
        if (\in_array($this->http->getModuleName(), self::APPLICABLE_MODULES))
            return true;
         if (\strpos($this->http->getRequestUri(), 'recovercart') !== false)
            return true;
        return false;
    }

    /**
     * Customer has active cart
     * @return boolean
     */
    public function hasActiveCart()
    {
        return ($this->cartHelper->getItemsCount())? true : false;
    }

    /**
     * Remove double quote happen on place order
     * @param  int $customerId
     * @return boolean
     */
    public function removeDoubleQuote($customerId)
    {
        try {
            $quote = $this->quoteRepo->getActiveForCustomer($customerId);
            $this->quoteRepo->delete($quote);
        } catch (NoSuchEntityException $e) {
            //
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Restore customer cart
     * @return \Magento\Quote\Model\Quote
     */
    public function restoreQuote()
    {
        return $this->session->restoreQuote();
    }

    /**
     * Restore customer cart without using session
     * @return \Magento\Quote\Model\Quote
     */
    public function manualRestore($quoteId)
    {
        $quote = $this->quoteRepo->get($quoteId);
        $quote->setIsActive(1)->setReservedOrderId(null);
        $this->quoteRepo->save($quote);
    }

}

