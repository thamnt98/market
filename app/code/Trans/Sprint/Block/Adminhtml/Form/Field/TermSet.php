<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Trans\Sprint\Block\Adminhtml\Form\Field\Select;

/**
 * Class TermSet
 */
class TermSet extends AbstractFieldArray
{
    /**
     * @var \Trans\Sprint\Block\Adminhtml\Form\Field\Select\EnableFactory
    */
    protected $enableDisable;

    /**
     * @var \Trans\Sprint\Block\Adminhtml\Form\Field\Select\TermFactory
     */
    protected $selectTerm;

    /**
     * @var \Trans\Sprint\Block\Adminhtml\Form\Field\Select\FileUploadFactory
     */
    protected $fileUpload;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Trans\Sprint\Block\Adminhtml\Form\Field\Select\TermFactory $selectTerm
     * @param \Trans\Sprint\Block\Adminhtml\Form\Field\Select\EnableFactory $enableDisable
     * @param \Trans\Sprint\Block\Adminhtml\Form\Field\Select\FileUploadFactory $fileUpload
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Trans\Sprint\Block\Adminhtml\Form\Field\Select\TermFactory $selectTerm,
        \Trans\Sprint\Block\Adminhtml\Form\Field\Select\EnableFactory $enableDisable,
        \Trans\Sprint\Block\Adminhtml\Form\Field\Select\FileUploadFactory $fileUpload,
        array $data = []
    ) {
        $this->fileUpload    = $fileUpload;
        $this->selectTerm    = $selectTerm;
        $this->enableDisable = $enableDisable;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD)
     */
    protected function _prepareToRender()
    {
        $this->addColumn('term', ['label' => __('Tenor'), 'class' => 'required-entry', 'renderer' => $this->selectTerm->create()]);
        $this->addColumn('channelId', ['label' => __('Channel ID'), 'class' => 'required-entry']);
        $this->addColumn('serviceFee', ['label' => __('Service Fee'), 'class' => 'required-entry validate-number']);
        // $this->addColumn('termImage', ['label' => __('Image'), 'renderer' => $this->fileUpload->create()]);
        $this->addColumn('enable', ['label' => __('Enable'), 'class' => 'required-entry', 'renderer' => $this->enableDisable->create()]);
        $this->_addAfter = false;
    }
}
