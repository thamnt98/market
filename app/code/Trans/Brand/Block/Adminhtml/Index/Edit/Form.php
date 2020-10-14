<?php
/**
 * Class Form
 *
 * PHP version 7
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
namespace Trans\Brand\Block\Adminhtml\Index\Edit;

/**
 * Class Form
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare Form
     *
     * @return \Magento\Backend\Block\Widget\Form\Generic
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post' ,
            'enctype' => 'multipart/form-data']]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
