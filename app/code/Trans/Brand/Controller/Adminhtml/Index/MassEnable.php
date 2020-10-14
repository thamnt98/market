<?php
/**
 * Class MassEnable
 *
 * PHP version 7
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
namespace Trans\Brand\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Trans\Brand\Model\ResourceModel\Brand\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassEnable
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
class MassEnable extends \Magento\Backend\App\Action
{
    /**
     * UiMassActionFilter
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * BrandCollectionFactory
     *
     * @var \Trans\Brand\Model\ResourceModel\Brand\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * MassEnable Contrsuctor
     *
     * @param Context           $context           context
     * @param Filter            $filter            UiMassActionFilter
     * @param CollectionFactory $collectionFactory BrandCollectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Trans_Brand::save');
    }

    /**
     * MassEnable Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     *
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection(
            $this->collectionFactory->create()
        );

        foreach ($collection as $item) {
            $item->setStatus(true);
            $this->saveItem($item);
        }

        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been enabled.', $collection->getSize())
        );

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Save Item
     *
     * @param CollectionFactory $item item
     *
     * @return void
     */
    public function saveItem($item)
    {
        $item->save();
    }
}
