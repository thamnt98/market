<?php

namespace SM\Review\Block\Customer;

use Magento\Framework\View\Element\Template;

/**
 * Class Tabs
 * @package SM\Review\Block\Customer
 */
class Tabs extends \Magento\Framework\View\Element\Template
{
    /**
     * Tabs constructor.
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @param $type
     * @return string
     */
    public function checkTabActive($type)
    {
        $tab = $this->_request->getParam('tab', 'to-be-reviewed');
        if ($tab == $type) {
            return 'active';
        } else {
            return '';
        }
    }

}
