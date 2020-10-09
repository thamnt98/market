<?php


namespace SM\Checkout\Controller\DigitalProduct;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Confirmed
 * @package SM\Checkout\Controller\DigitalProduct
 */
class Confirmed extends Action
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set(__('Order Confirmed'));
        return $resultPage;
    }
}
