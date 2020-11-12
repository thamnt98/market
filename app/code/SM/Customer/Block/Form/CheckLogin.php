<?php

namespace SM\Customer\Block\Form;

use Magento\Customer\Model\Context;

class CheckLogin extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Register constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
    }

    /**
     * Checking customer register status
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }

    protected function _prepareLayout()
    {
        if ($this->isLoggedIn()) {
            $this->pageConfig->addBodyClass('login');
        } else {
            $this->pageConfig->addBodyClass('logout');
        }

        return parent::_prepareLayout();
    }
}
