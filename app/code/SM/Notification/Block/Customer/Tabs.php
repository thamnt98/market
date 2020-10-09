<?php

namespace SM\Notification\Block\Customer;

class Tabs extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \SM\Notification\Helper\Data
     */
    protected $helper;

    /**
     * Tabs constructor.
     *
     * @param \SM\Notification\Helper\Data                     $helper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \SM\Notification\Helper\Data $helper,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
    }

    /**
     * @param $type
     * @return string
     */
    public function checkTabActive($type)
    {
        $tab = $this->_request->getParam('tab', 'all');
        if ($tab == $type) {
            return 'active';
        } else {
            return '';
        }
    }

    /**
     * @return array
     */
    public function getEventTypeList()
    {
        return $this->helper->getEventEnable();
    }
}
