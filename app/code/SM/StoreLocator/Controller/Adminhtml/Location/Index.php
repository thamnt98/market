<?php

namespace SM\StoreLocator\Controller\Adminhtml\Location;

use SM\StoreLocator\Controller\Adminhtml\AbstractLocationForm;

/**
 * Class Index
 * @package SM\StoreLocator\Controller\Adminhtml\Location
 */
class Index extends AbstractLocationForm
{

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('SM_StoreLocator::store_location')->_addBreadcrumb(__('Store Location'), __('Store Location'));
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Store Location Management'), __('Store Location Management'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Stores Location'));
        $this->_view->renderLayout();
    }
}
