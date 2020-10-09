<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Controller\Adminhtml\Category;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use SM\DigitalProduct\Controller\Adminhtml\Category;

/**
 * Class Delete
 * @package SM\DigitalProduct\Controller\Adminhtml\Category
 */
class Delete extends Category
{
    /**
     * Delete action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('category_id');
        if ($id) {
            try {
                $this->categoryRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the Category.'));
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['category_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a Category to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
