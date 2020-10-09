<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;
use Mirasvit\Blog\Api\Data\CategoryInterface;
use Mirasvit\Blog\Model\CategoryFactory;

/**
 * Class Save
 * @package SM\InspireMe\Controller\Adminhtml\Category
 */
class Save extends \Mirasvit\Blog\Controller\Adminhtml\Category
{
    /**
     * @var \Mirasvit\Blog\Api\Repository\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Mirasvit\Blog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Save constructor.
     * @param CategoryFactory $authorFactory
     * @param \Mirasvit\Blog\Api\Repository\CategoryRepositoryInterface $categoryRepository
     * @param \Mirasvit\Blog\Model\ResourceModel\Category\CollectionFactory $collectionFactory
     * @param Registry $registry
     * @param Context $context
     */
    public function __construct(
        CategoryFactory $authorFactory,
        \Mirasvit\Blog\Api\Repository\CategoryRepositoryInterface $categoryRepository,
        \Mirasvit\Blog\Model\ResourceModel\Category\CollectionFactory $collectionFactory,
        Registry $registry,
        Context $context
    ) {
        parent::__construct($authorFactory, $registry, $context);
        $this->categoryRepository = $categoryRepository;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $id             = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data = $this->getRequest()->getParams()) {
            $model = $this->initModel();

            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This topic no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            $data = $this->prepareData($data);
            $model->addData($data);

            try {
                $this->categoryRepository->save($model);
                $this->messageManager->addSuccessMessage(__('Topic was successfully saved'));
                $this->context->getSession()->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }

                return $this->context->getResultRedirectFactory()->create()->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        } else {
            $resultRedirect->setPath('*/*/');
            $this->messageManager->addErrorMessage('No data to save.');

            return $resultRedirect;
        }
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareData(array $data)
    {
        /** @var \Mirasvit\Blog\Model\Category $rootTopic */
        $rootTopic = $this->collectionFactory->create()
            ->addFieldToFilter(CategoryInterface::PARENT_ID, ['eq' => 0])
            ->getFirstItem();

        $data[CategoryInterface::PARENT_ID] = $rootTopic->getId() ? $rootTopic->getId() : 0;

        return $data;
    }
}
