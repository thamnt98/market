<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Controller\Adminhtml\Category;

use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use SM\DigitalProduct\Controller\Adminhtml\Category;

/**
 * Class Edit
 * @package SM\DigitalProduct\Controller\Adminhtml\Category
 */
class Edit extends Category
{
    /**
     * Edit action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('category_id');

        /** @var \SM\DigitalProduct\Model\Category $model */
        $model = $this->categoryFactory->create();

        // 2. Initial checking
        if ($id) {
            try {
                $model = $this->categoryRepository->get($id);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(__('This Category no longer exists.'));
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        // 3. Build edit form
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Category') : __('New Category'),
            $id ? __('Edit Category') : __('New Category')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Categories'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ?
            __('Edit Category %1', $this->typeHelper->getTypeOptions()[$model->getType()]) : __('New Category'));
        return $resultPage;
    }
}
