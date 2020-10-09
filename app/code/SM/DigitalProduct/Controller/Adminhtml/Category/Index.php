<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Controller\Adminhtml\Category;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use SM\DigitalProduct\Controller\Adminhtml\Category;

/**
 * Class Index
 * @package SM\DigitalProduct\Controller\Adminhtml\Category
 */
class Index extends Category
{
    /**
     * Index action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__("Category"));
        return $resultPage;
    }
}
