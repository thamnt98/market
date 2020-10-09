<?php
/**
 * Class Edit
 * @package SM\Theme\Controller\Adminhtml\Lookbook
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Theme\Controller\Adminhtml\Lookbook;

class Edit extends \MGS\Lookbook\Controller\Adminhtml\Lookbook
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Backend\Model\Session
     */
    private $session;

    /**
     * @var \MGS\Lookbook\Model\LookbookFactory
     */
    private $lookbookFactory;

    /**
     * @var \MGS\Lookbook\Model\ResourceModel\Lookbook
     */
    private $lookbookResource;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \MGS\Lookbook\Model\LookbookFactory $lookbookFactory
     * @param \MGS\Lookbook\Model\ResourceModel\Lookbook $lookbookResource
     * @param \Magento\Backend\Model\Session $session
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \MGS\Lookbook\Model\LookbookFactory $lookbookFactory,
        \MGS\Lookbook\Model\ResourceModel\Lookbook $lookbookResource,
        \Magento\Backend\Model\Session $session
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->lookbookFactory = $lookbookFactory;
        $this->lookbookResource = $lookbookResource;
        $this->session = $session;
        parent::__construct($context);
    }

    /**
     * Edit sitemap
     *
     * @return void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        /**
         * \MGS\Lookbook\Model\Lookbook $model
         */
        $model = $this->lookbookFactory->create();

        // 2. Initial checking
        if ($id) {
            $this->lookbookResource->load($model, $id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This item no longer exists.'));
                $this->_redirect('adminhtml/*/');
                return;
            }
        }

        // 3. Set entered data if was error when we do save
        $data = $this->session->getFormData(true);

        if (!empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        $this->_coreRegistry->register('lookbook_lookbook', $model);

        // 5. Build edit form
        $this->_initAction()->_addBreadcrumb(
            $id ? __('Edit %1', $model->getName()) : __('New Hot Spot'),
            $id ? __('Edit %1', $model->getName()) : __('New Hot Spot')
        )->_addContent(
            $this->_view->getLayout()->createBlock('MGS\Lookbook\Block\Adminhtml\Edit')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Hot Spot'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getName() : __('New Hot Spot')
        );
        $this->_view->renderLayout();
    }
}
