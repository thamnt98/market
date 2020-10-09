<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Controller\Adminhtml\OperatorIcon;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use SM\DigitalProduct\Model\OperatorIcon;

/**
 * Class Delete
 * @package SM\DigitalProduct\Controller\Adminhtml\OperatorIcon
 */
class Delete extends \SM\DigitalProduct\Controller\Adminhtml\OperatorIcon
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
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('operator_icon_id');
        if ($id) {
            try {
                $this->repository->deleteById($id);
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Operator Icon.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['operator_icon_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Operator Icon to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
