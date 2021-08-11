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
namespace Trans\Mepay\Model\CardSavedToken;

use Trans\Mepay\Api\Data\CardSavedTokenInterface;
use Trans\Mepay\Api\Data\CardSavedTokenInterfaceFactory;
use Magento\Checkout\Model\Session;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Trans\Mepay\Gateway\Request\TokenDataBuilder;
use Trans\Mepay\Helper\Customer\Customer as CustomerHelper;
use Trans\Mepay\Helper\Quote as QuoteHelper;

class Builder
{
    /**
     * @var \Trans\Mepay\Api\Data\CardSavedTokenInterfaceFactory
     */
    protected $modelFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;

    protected $cartRepo;

    /**
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    protected $userContext;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Trans\Mepay\Helper\Customer\Customer
     */
    protected $custHelper;

    protected $quoteHelper;

    /**
     * Constructor
     *
     * @param Session $session
     * @param CardSavedTokenInterfaceFactory $modelFactory
     * @param SerializerInterface $serializer
     * @param CustomerHelper $custHelper
     */
    public function __construct(
        Session $session,
        CartRepositoryInterface $cartRepo,
        UserContextInterface $userContext,
        CardSavedTokenInterfaceFactory $modelFactory,
        QuoteHelper $quoteHelper,
        SerializerInterface $serializer,
        CustomerHelper $custHelper
    ) {
        $this->session = $session;
        $this->cartRepo = $cartRepo;
        $this->userContext = $userContext;
        $this->modelFactory = $modelFactory;
        $this->serializer = $serializer;
        $this->custHelper = $custHelper;
        $this->quoteHelper = $quoteHelper;
    }

    /**
     * Get all token
     *
     * @return \Trans\Mepay\Api\Data\CardSavedTokenInterface[]
     */
    public function getAll()
    {
        return $this->wrap($this->_getAll());
    }

    /**
     * Get token by payment method
     *
     * @param string $method
     * @return \Trans\Mepay\Api\Data\CardSavedTokenInterface
     */
    public function getByMethod($method)
    {
        $data = $this->wrap($this->_getByMethod($method));
        return $this->wrap($this->_getByMethod($method));
    }

    /**
     * Save token
     *
     * @param CardSavedTokenInterface $model
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function save(CardSavedTokenInterface $model)
    {
        $lists = $this->_getByMethod($model->getMethod());
        foreach($lists as $key => $value)
        {
            if($value->getKey() == $model->getKey()) {
                $lists[$key] = $model;
            }
        }
        return $this->_save($method, $lists);
    }

    /**
     * Save token
     *
     * @param CardSavedTokenInterface $model
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function setCustomerToken($token, string $method)
    {
        $quote = $this->cartRepo->getActiveForCustomer($this->userContext->getUserId());
        $this->upadateCardTokenByQuoteId($quote->getId(), $token);
    }

    public function deleteCustomerToken(string $token, string $method)
    {
        $tokenlist = $this->_getByMethod($method);
        $tokenlist = $this->quoteHelper->removeTokenOnTokenlist($tokenlist, $token);
        $this->quoteHelper->setCustomerTokenlist($this->userContext->getUserId(), $method, $tokenlist);
    }

    /**
     * Update token by quote id
     *
     * @param int $id
     * @param string $token
     * @return void
     */
    protected function upadateCardTokenByQuoteId($id, $token)
    {
        $this->quoteHelper->setCardTokenByQuoteId($id, $token);
    }

    /**
     * Get by method
     *
     * @param string $method
     * @return mixed[]
     */
    protected function _getByMethod($method)
    {
        $list = $this->custHelper->getCustomerToken($this->userContext->getUserId(), $method);
        if (substr($list,0,1) == '"')
            $this->serializer->unserialize($list);
        return $this->serializer->unserialize($list);
    }

    /**
     * Get all token
     *
     * @return mixed[]
     */
    protected function _getAll()
    {
        $result = [];
        foreach (CardSavedTokenInterface::getMethodKeys() as $key => $value) {
            $data = $this->_getByMethod($value);
            foreach ($data as $content) {
                $result[] = $content;
            }
        }
        return $result;
    }

    /**
     * Save token
     *
     * @param string $method
     * @param mixed[] $lists
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    protected function _save($method, $lists)
    {
        $result = [];
        foreach ($lists as $key => $value) {
            $result[] = $value->getData();
        }
        $result = $this->serializer->serialize($result);
        $this->custHelper->setCustomerToken($this->userContext->getUserId(), $method, $result);

    }

    /**
     * Wrap array with object
     *
     * @param mixed[] $list
     * @return \Magento\Customer\Api\Data\CustomerInterface[]
     */
    protected function wrap($list)
    {
        $result = [];
        if (is_array($list)) {
            foreach ($list as $key => $value) {
                $model = $this->modelFactory->create();
                $model->setKey($value[CardSavedTokenInterface::KEY]);
                $model->setMethod($value[CardSavedTokenInterface::METHOD]);
                $model->setToken($value[TokenDataBuilder::TOKEN]);
                $result[] = $model;
            }
        }
        
        return $result;
    }
}