<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Controller\Adminhtml\Category;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use SM\DigitalProduct\Controller\Adminhtml\Category;

/**
 * Class Save
 * @package SM\DigitalProduct\Controller\Adminhtml\Category
 */
class Save extends Category
{
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
            $id = $this->getRequest()->getParam('category_id');

            try {
                $categoryModel = $this->categoryRepository->get($id);
            } catch (LocalizedException $e) {
                if ($id) {
                    $this->messageManager->addErrorMessage(__('This Category no longer exists.'));
                    /** @var Redirect $resultRedirect */
                    $resultRedirect = $this->resultRedirectFactory->create();
                    return $resultRedirect->setPath('*/*/');
                } else {
                    /** @var \SM\DigitalProduct\Model\Category $categoryModel */
                    $categoryModel = $this->categoryFactory->create();
                }
            }

            $originalData = $data;
            $this->dataPersistor->set('sm_digitalproduct_category', $originalData);

            $categoryModel->setData($data);
            if (!isset($data["information"])) {
                $data["information"] = "";
            }
            if (!isset($data["tooltip"])) {
                $data["tooltip"] = "";
            }
            if (!isset($data["info"])) {
                $data["info"] = "";
            }
            try {
                /** @var \SM\DigitalProduct\Model\Category $category */
                $categoryModel = $this->categoryRepository->save($categoryModel);

                $this->messageManager->addSuccessMessage(__('You saved the Category.'));
                $this->dataPersistor->clear('sm_digitalproduct_category');

                if ($this->getRequest()->getParam('back') == "continue") {
                    if ($data["store_id"] != 0) {
                        return $resultRedirect->setPath(
                            '*/*/edit',
                            [
                                'category_id' => $categoryModel->getId(),
                                'store' => $data["store_id"]
                            ]
                        );
                    }
                    return $resultRedirect->setPath('*/*/edit', ['category_id' => $categoryModel->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __($e->getMessage()));
            }

            if (isset($originalData["store_id"])) {
                if ($originalData["store_id"] != 0) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'category_id' => $categoryModel->getId(),
                            'store' => $originalData["store_id"]
                        ]
                    );
                }
            }

            return $resultRedirect->setPath(
                '*/*/edit',
                ['category_id' => $this->getRequest()->getParam('category_id')]
            );
        }
        return $resultRedirect->setPath('*/*/');
    }
}
