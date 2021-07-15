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
use Magento\Framework\App\ResourceConnection;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Trans\Mepay\Api\Data\CardSavedTokenInterface;
use Trans\Mepay\Helper\Data;

class Quote extends AbstractHelper
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $connection;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepo;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var string
     */
    protected $quoteTable;

    /**
     * @var string
     */
    protected $quotePaymentTable;

    /**
     * Constructor
     *
     * @param Context $context
     * @param ResourceConnection $connection
     * @param CustomerRepositoryInterface $customerRepo
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Context $context,
        ResourceConnection $connection,
        CustomerRepositoryInterface $customerRepo,
        SerializerInterface $serializer
    ){
        $this->connection = $connection->getConnection();
        $this->quoteTable = $this->connection->getTableName('quote');
        $this->quotePaymentTable = $this->connection->getTableName('quote_payment');
        $this->customerRepo = $customerRepo;
        $this->serializer = $serializer;
        parent::__construct($context);
    }

    /**
     * Set card token by quote id
     *
     * @param int $id
     * @param token $token
     * @return void
     */
    public function setCardTokenByQuoteId($id, $token)
    {
        $query = "UPDATE ".$this->quotePaymentTable." SET card_token = '".$token."' WHERE quote_id = '".$id."'";
        $this->connection->query($query);
    }

    /**
     * Remove token by quote id
     *
     * @param int $customerId
     * @param int $quoteId
     * @return void
     */
    public function removeTokenByQuoteId($customerId, $quoteId)
    {
        if($token = $this->getCardTokenByQuoteId($quoteId)){
            return $this->_removeToken($customerId, $token);
        }
        return false;
    }

    /**
     * Get card token by id
     *
     * @param integer $id
     * @return void
     */
    public function getCardTokenByQuoteId(int $id)
    {
        $query = "SELECT card_token FROM ".$this->quotePaymentTable." WHERE quote_id = '".$id."'";
        $cardToken = $this->connection->fetchRow($query);
        return $cardToken['card_token'];
    }

    /**
     * Remove token on tokenlist
     *
     * @param array $tokenlist
     * @param string $token
     * @return string
     */
    public function removeTokenOnTokenlist($tokenlist, $token)
    {
        foreach ($tokenlist as $index => $content) {
            if ($content['token'] == $token) {
                $flag = true;
                unset($tokenlist[$index]);
            }
        }
        return $this->serializer->serialize($tokenlist);
    }

    /**
     * Save customer tokenlist
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param string $method
     * @param string $tokenlist
     * @return void
     */
    public function saveCustomerTokenlist($customer, $method, $tokenlist)
    {
        $customer->setCustomAttribute($method.'_'.CardSavedTokenInterface::CARDTOKEN, $tokenlist);
        $this->customerRepo->save($customer);
        return true;
    }

    /**
     * Set customer tokenlist
     *
     * @param int $customerId
     * @param string $method
     * @param string $tokenlist
     * @return void
     */
    public function setCustomerTokenlist($customerId, $method, $tokenlist)
    {
        $customer = $this->customerRepo->getById($customerId);
        $this->saveCustomerTokenlist($customer, $method, $tokenlist);
    }

    /**
     * Remove customer token
     *
     * @param int $customerId
     * @param string $token
     * @return void
     */
    protected function _removeToken($customerId, $token)
    {
        $customer = $this->customerRepo->getById($customerId);
        foreach (Data::BANK_MEGA_PAYMENT_METHOD as $key => $value) {
            $flag = false;
            if ($list = $customer->getCustomAttribute($value.'_'.CardSavedTokenInterface::CARDTOKEN)) {
                if (substr($list->getValue(),0,1) == '"') {
                    $data = $this->serializer->unserialize($list->getValue());
                    $data = $this->serializer->unserialize($data);
                } else {
                    $data = $this->serializer->unserialize($list->getValue());
                }
                foreach ($data as $index => $content) {
                    if ($content['token'] == $token) {
                        $flag = true;
                        unset($data[$index]);
                    }
                }
                if ($flag) {
                    $data = $this->serializer->serialize($data);
                    return $this->saveCustomerTokenlist($customer, $value, $data);
                }
            }
        }
        return false;
    }
}