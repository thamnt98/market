<?php

namespace SM\MobileApi\Controller\Product;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Image360
 * @package SM\MobileApi\Controller\Product
 */
class Image360 extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     * @param PageFactory $resultPageFactory
     * @param Context $context
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Context $context
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface|Page
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $result = $this->resultPageFactory->create();

        if (isset($params['product_id'])) {
            $productId = $params['product_id'];
            $block = $result->getLayout()->getBlock('product_image_360');
            $block->setData('product_id', $productId);
        }

        return $result;
    }
}
