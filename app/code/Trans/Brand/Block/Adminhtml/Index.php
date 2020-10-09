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
namespace Trans\Brand\Block\Adminhtml;

/**
 * Class Index
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
class Index extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Index Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::__construct();
    }

    /**
     * Check user is allowed to access resource
     *
     * @param int $resourceId resourceId
     *
     * @return boolean
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
