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
namespace Trans\Brand\Block\Adminhtml\Index;

/**
 * Class Edit
 *
 * @category Trans
 * @package  Trans_Brand
 * @author   J.P <jaka.pondan@transdigital.co.id>
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Edit Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context  context
     * @param \Magento\Framework\Registry           $registry CoreRegistry
     * @param array                                 $data     data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize blog Brand edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'brand_id';
        $this->_blockGroup = 'Trans_Brand';
        $this->_controller = 'adminhtml_index';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Brand'));
        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ],
                    ],
                ]
            ],
            -100
        );
        
        if ($this->isAllowedAction('Trans_Brand::brand_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Brand'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded Brand
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->coreRegistry->registry('brands_index')->getId()) {
            return __(
                "Edit Brand '%1'",
                $this->escapeHtml(
                    $this->coreRegistry->registry('brands_index')->getTitle()
                )
            );
        } else {
            return __('New Brand');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId resourceId
     *
     * @return bool
     */
    protected function isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function getSaveAndContinueUrl()
    {
        return $this->getUrl(
            'brands/*/save',
            ['_current' => true, 'back' => 'edit', 'active_tab' => '']
        );
    }
}
