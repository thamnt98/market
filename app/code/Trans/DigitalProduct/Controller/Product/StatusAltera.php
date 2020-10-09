<?php
/**
 * @category Trans
 * @package  Trans_DigitalProduct
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   hadi <ashadi.sejati@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\DigitalProduct\Controller\Product;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\Controller\Result\JsonFactory;
use Trans\DigitalProduct\Api\DigitalProductStatusInterface;
use Trans\DigitalProduct\Helper\Data;
use Magento\Framework\View\Result\PageFactory;

/**
 * Controller
 */
class StatusAltera extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
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
     * @var DigitalProductStatusInterface
     */
    protected $digitalProductStatusInterface;

    /**
    * @var PageFactory
    */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param Data $dataHelper
     * @param JsonFactory $resultJsonFactory
     * @param DigitalProductStatusInterface $digitalProductStatusInterface
     */
    public function __construct(
        Context $context,
        Data $dataHelper,
        JsonFactory $resultJsonFactory,
        DigitalProductStatusInterface $digitalProductStatusInterface,
        PageFactory $pageFactory
    ) {
        parent::__construct($context);

        $this->dataHelper                         = $dataHelper;
        $this->resultJsonFactory                  = $resultJsonFactory;
        $this->digitalProductStatusInterface      = $digitalProductStatusInterface;
        $this->digitalProductStatusInterface      = $digitalProductStatusInterface;
        $this->resultPageFactory = $pageFactory;

        $this->config = $this->dataHelper->getConfigHelper();
        $this->logger = $this->dataHelper->getLogger();
    }

    public function execute()
    {
        $dataReq = $this->getRequest()->getContent();
        $data = $this->digitalProductStatusInterface->getCallbackAltera($dataReq);
        $result = $this->resultJsonFactory->create();
        //$postData = $this->getRequest()->getParams();
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
