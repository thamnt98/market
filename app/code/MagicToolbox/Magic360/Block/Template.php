<?php

namespace MagicToolbox\Magic360\Block;

/**
 * Magic 360 view template block
 *
 */
class Template extends \Magento\Framework\View\Element\Template
{
    /**
     * Default template
     *
     * @var string
     */
    protected $defaultTemplate = 'MagicToolbox_Magic360::product/view/layouts/bottom.phtml';

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        if (isset($data['layout'])) {
            $this->setTemplate('MagicToolbox_Magic360::product/view/layouts/' . $data['layout'] . '.phtml');
        } else {
            $this->setTemplate($this->defaultTemplate);
        }

        parent::__construct($context, $data);
    }
}
