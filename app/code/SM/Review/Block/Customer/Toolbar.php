<?php

namespace SM\Review\Block\Customer;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\App\Response\Http;

class Toolbar extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CurrentCustomer
     */
    private $currentCustomer;
    /**
     * @var Http
     */
    private $response;

    protected $tab;

    /**
     * Toolbar constructor.
     * @param Template\Context $context
     * @param CurrentCustomer $currentCustomer
     * @param Http $response
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CurrentCustomer $currentCustomer,
        Http $response,
        array $data = []
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->response = $response;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->tab = $this->_request->getParam('tab', 'to-be-reviewed');
        parent::_construct();
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->getUrl('*/*/*', ['tab' => $this->tab, '_current' => true, '_use_rewrite' => true]);
    }
}
