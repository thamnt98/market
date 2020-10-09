<?php

namespace Trans\DigitalProduct\Controller\Product;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\Controller\Result\JsonFactory;
use Trans\DigitalProduct\Api\DigitalProductInquireInterface;
use Trans\DigitalProduct\Api\DigitalProductTransactionInterface;
use Trans\DigitalProduct\Api\DigitalProductGetOperatorInterface;
use Trans\DigitalProduct\Helper\Data;

/**
 * Controller
 */
class Test extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var DigitalProductInquireInterface
     */
    protected $digitalProductInquireInterface;

    /**
     * @var DigitalProductTransactionInterface
     */
    protected $digitalProductTransactionInterface;

    /**
     * @param Context $context
     * @param Data $dataHelper
     * @param JsonFactory $resultJsonFactory
     * @param DigitalProductInquireInterface $digitalProductInquireInterface
     * @param DigitalProductTransactionInterface $digitalProductTransactionInterface
     */
    public function __construct(
        Context $context,
        Data $dataHelper,
        JsonFactory $resultJsonFactory,
        DigitalProductInquireInterface $digitalProductInquireInterface,
        DigitalProductTransactionInterface $digitalProductTransactionInterface
    ) {
        parent::__construct($context);

        $this->dataHelper                         = $dataHelper;
        $this->resultJsonFactory                  = $resultJsonFactory;
        $this->digitalProductInquireInterface     = $digitalProductInquireInterface;
        $this->digitalProductTransactionInterface = $digitalProductTransactionInterface;

        $this->config = $this->dataHelper->getConfigHelper();
        $this->logger = $this->dataHelper->getLogger();
    }

    public function execute()
    {
        $customerId     = "1";
        $customerNumber = "01428800700";
        $productId      = "25";
        $orderId        = "TRANSMARTelec00061";
        $meterNumber    = "01428800700";
        $paymentPeriod  = "01";
        $operatorCode   = "pdam_aetra";

        //$data = $this->digitalProductInquireInterface->electricity($customerId, $customerNumber, $productId);//inquiry
        $data = $this->digitalProductTransactionInterface->electricity($customerId, $customerNumber, $meterNumber, $productId, $orderId); // create transaction
        //$data = $this->digitalProductTransactionInterface->pdam($productId); // cek operator
        $result = $this->resultJsonFactory->create();
        return $result->setData($data);
    }

    /**
     * @param  RequestInterface $request
     * @return mixing
     */
    public function createCsrfValidationException(RequestInterface $request):  ? InvalidRequestException
    {
        return null;
    }

    /**
     * @param  RequestInterface $request
     * @return boolean
     */
    public function validateForCsrf(RequestInterface $request) :  ? bool
    {
        return true;
    }
}
