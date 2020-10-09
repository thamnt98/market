<?php

/**
 * @category SM
 * @package SM_Help
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author      Chinhvd <chinhvd@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Help\Controller\ContactUs;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class GetProduct extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        PageFactory $resultPageFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Get selected order product
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $resultPage = $this->resultPageFactory->create();
        try {
            $orderId = $this->getRequest()->getParam('orderId');
            if ($orderId) {
                $data = array('orderId' => $orderId);
                $block = $resultPage->getLayout()
                    ->createBlock('SM\Help\Block\ContactUs\OrderProduct')
                    ->setTemplate('SM_Help::help/return-refund-product.phtml')
                    ->setData('data', $data)
                    ->toHtml();

                $resultJson->setData(['output' => $block]);
            }
            return $resultJson;
        } catch (\Exception $e) {
            return $resultJson->setData('false');
        }
    }
}
