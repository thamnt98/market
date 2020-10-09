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

namespace Trans\IntegrationCatalogPrice\Controller\Promotion;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\Controller\Result\JsonFactory;
use Trans\IntegrationCatalogPrice\Api\PromotionPriceLogicInterface;
use Magento\Framework\View\Result\PageFactory;

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
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var PromotionPriceLogicInterface
     */
    protected $promotionPriceLogicInterface;

    /**
    * @var PageFactory
    */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param PromotionPriceLogicInterface $promotionPriceLogicInterface
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        PromotionPriceLogicInterface $promotionPriceLogicInterface,
        PageFactory $pageFactory
    ) {
        parent::__construct($context);

        $this->resultJsonFactory                  = $resultJsonFactory;
        $this->promotionPriceLogicInterface      = $promotionPriceLogicInterface;
        $this->resultPageFactory = $pageFactory;
    }

    public function execute()
    {
        $dataReq = $this->getRequest()->getContent();
        $data = $this->promotionPriceLogicInterface->saveTest($dataReq);
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
