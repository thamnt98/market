<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Controller\Adminhtml\OperatorIcon;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use SM\DigitalProduct\Model\OperatorIcon;
use SM\DigitalProduct\Model\OperatorIconFactory;
use SM\DigitalProduct\Model\OperatorIconRepository;

/**
 * Class Edit
 * @package SM\DigitalProduct\Controller\Adminhtml\OperatorIcon
 */
class Edit extends \SM\DigitalProduct\Controller\Adminhtml\OperatorIcon
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param OperatorIconFactory $modelFactory
     * @param OperatorIconRepository $repository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        OperatorIconFactory $modelFactory,
        OperatorIconRepository $repository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $modelFactory, $repository);
    }

    /**
     * Edit action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('operator_icon_id');

        /** @var OperatorIcon $model */
        $model = $this->modelFactory->create();
        // 2. Initial checking
        if ($id) {
            try {
                /** @var OperatorIcon $model */
                $model = $this->repository->get($id);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(__('This Operator Icon no longer exists.'));
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        // 3. Build edit form
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Operator Icon') : __('New Operator Icon'),
            $id ? __('Edit Operator Icon') : __('New Operator Icon')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Operator Icons'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ?
            __('Edit Operator Icon %1', $model->getId()) : __('New Operator Icon'));
        return $resultPage;
    }
}
