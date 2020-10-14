<?php
/**
 * Class Index
 *
 * PHP version 7
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
namespace Trans\Brand\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * ResultPageFactory
     *
     * @var bool|PageFactory
     */
    protected $resultPageFactory = false;

    /**
     * Index Constructor
     *
     * @param Context     $context           context
     * @param PageFactory $resultPageFactory pageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Trans_Brand::brand');
        $resultPage->addBreadcrumb(__('Brands'), __('Brands'));
        $resultPage->addBreadcrumb(__('Manage Brands'), __('Manage Brands'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Brands'));

        return $resultPage;
    }

    /**
     * Is the user allowed to view the brand grid.
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Trans_Brand::brand');
    }
}
