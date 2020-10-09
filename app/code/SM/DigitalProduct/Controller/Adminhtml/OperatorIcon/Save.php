<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Controller\Adminhtml\OperatorIcon;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\DB\Adapter\DuplicateException;
use Magento\Framework\Exception\LocalizedException;
use SM\DigitalProduct\Api\Data\OperatorIconInterface;
use SM\DigitalProduct\Controller\Adminhtml\OperatorIcon;
use SM\DigitalProduct\Model\OperatorIconFactory;
use SM\DigitalProduct\Model\OperatorIconRepository;

/**
 * Class Save
 * @package SM\DigitalProduct\Controller\Adminhtml\OperatorIcon
 */
class Save extends OperatorIcon
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param OperatorIconFactory $modelFactory
     * @param OperatorIconRepository $repository
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        OperatorIconFactory $modelFactory,
        OperatorIconRepository $repository
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context, $modelFactory, $repository);
    }

    /**
     * Save action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam(OperatorIconInterface::OPERATOR_ICON_ID);

            try {
                /** @var \SM\DigitalProduct\Model\OperatorIcon $model */
                $model = $this->repository->get($id);
            } catch (LocalizedException $e) {
                if ($id) {
                    $this->messageManager->addErrorMessage(__('This Operator Icon no longer exists.'));
                    /** @var Redirect $resultRedirect */
                    $resultRedirect = $this->resultRedirectFactory->create();
                    return $resultRedirect->setPath('*/*/');
                } else {
                    /** @var \SM\DigitalProduct\Model\OperatorIcon $model */
                    $model = $this->modelFactory->create();
                }
            }
            $originalData = $data;
            $this->dataPersistor->set('sm_digitalproduct_operator_icon', $originalData);
            if (isset($data["icon"])) {
                if (isset($data["icon"][0])) {
                    $data["icon"] = json_encode($data["icon"][0]);
                } else {
                    $data["icon"] = "";
                }
            } else {
                $data["icon"] = "";
            }

            $model->setData($data);
            try {
                $this->repository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the Operator Icon.'));
                $this->dataPersistor->clear('sm_digitalproduct_operator_icon');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['operator_icon_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (DuplicateException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the Operator Icon.')
                );
            }

            return $resultRedirect->setPath(
                '*/*/edit',
                ['operator_icon_id' => $this->getRequest()->getParam('operator_icon_id')]
            );
        }
        return $resultRedirect->setPath('*/*/');
    }
}
