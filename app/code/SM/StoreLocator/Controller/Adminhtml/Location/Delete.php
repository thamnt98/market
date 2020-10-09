<?php

namespace SM\StoreLocator\Controller\Adminhtml\Location;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use SM\StoreLocator\Model\Repository\StoreLocationRepository;

class Delete extends Action implements HttpPostActionInterface
{
    /**
     * @var StoreLocationRepository
     */
    protected $locationRepository;

    /**
     * Delete constructor.
     * @param Action\Context $context
     * @param StoreLocationRepository $locationRepository
     */
    public function __construct(Action\Context $context, StoreLocationRepository $locationRepository)
    {
        parent::__construct($context);
        $this->locationRepository = $locationRepository;
    }

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
        $id = $this->getRequest()->getParam('place_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->locationRepository->deleteStoreById($id);
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the location.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['place_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a location to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
