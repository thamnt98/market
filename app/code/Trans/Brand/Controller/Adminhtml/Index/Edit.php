<?php
/**
 * Class Edit
 *
 * PHP version 7
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
namespace Trans\Brand\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\Session;
use Magento\Framework\Registry;

/**
 * Class Edit
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * PageFactory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * BrandFactory
     *
     * @var \Trans\Brand\Model\BrandFactory
     */
    protected $brand;

    /**
     * SessionModel
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $sessionData;

    /**
     * Edit Constructor
     *
     * @param Action\Context $context           context
     * @param PageFactory    $resultPageFactory pageFactory
     * @param BrandFactory   $brand             brandModel
     * @param Session        $session           sessionModel
     * @param Registry       $registry          coreRegistry
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        \Trans\Brand\Model\BrandFactory $brand,
        Session $session,
        Registry $registry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        $this->brand = $brand;
        $this->sessionData = $session;
    }
    
    /**
     * {@inheritdoc}
     *
     @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Trans_Brand::save');
    }
    
    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }

    /**
     * Edit action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('brand_id');
        $model = $this->brand->create();

        if ($id) {
            $model = $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('Something went wrong.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->sessionData->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->coreRegistry->register('brands_index', $model);

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Brand') : __('New Brand'),
            $id ? __('Edit Brand') : __('New Brand')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Brand'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('New Brand'));

        return $resultPage;
    }
}
