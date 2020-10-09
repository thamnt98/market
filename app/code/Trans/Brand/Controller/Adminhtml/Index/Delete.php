<?php
/**
 * Class Delete
 *
 * PHP version 7
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
namespace Trans\Brand\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

/**
 * Class Delete
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * BrandFactory
     *
     * @var \Trans\Brand\Model\BrandFactory
     */
    protected $brandFactory;

    /**
     * UrlRewriteFactory
     *
     * @var \Magento\UrlRewrite\Model\UrlRewriteFactory
     */
    protected $urlRewriteFactory;

    /**
     * Delete constructor.
     *
     * @param Action\Context                              $context           context
     * @param \Trans\Brand\Model\BrandFactory        $brandFactory      brandFactory
     * @param \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteFactory urlRewriteFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Trans\Brand\Model\BrandFactory $brandFactory,
        \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteFactory
    ) {
        parent::__construct($context);
        $this->brandFactory = $brandFactory;
        $this->urlRewriteFactory = $urlRewriteFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Trans_Brand::delete');
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('brand_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $brandModel = $this->brandFactory->create();
                $brandModel->load($id);
                
                $urlRewriteModel = $this->urlRewriteFactory->create();
                $urlRewriteData = $urlRewriteModel->getCollection()
                    ->addFieldToFilter('request_path', $brandModel->getUrlKey());
                
                foreach ($urlRewriteData->getItems() as $rewrite) {
                    $this->deleteItem($rewrite);
                }
                
                $brandModel->delete();

                $this->messageManager->addSuccess(__('The brand has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['brand_id' => $id]);
            }
        }

        $this->messageManager->addError(__('We can\'t find a brand to delete.'));
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Delete urlrewrite item
     *
     * @param \Magento\UrlRewrite\Model\UrlRewriteFactory $item item
     *
     * @return void
     */
    public function deleteItem($item)
    {
        $item->delete();
    }
}
