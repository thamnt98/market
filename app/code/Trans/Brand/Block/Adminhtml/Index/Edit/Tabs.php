<?php
/**
 * Class Tabs
 *
 * PHP version 7
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
namespace Trans\Brand\Block\Adminhtml\Index\Edit;

/**
 * Class Tabs
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Initialize Tabs Block
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('brand_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Brand Information'));
    }
}
