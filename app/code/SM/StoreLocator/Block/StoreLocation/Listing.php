<?php
namespace SM\StoreLocator\Block\StoreLocation;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use SM\StoreLocator\Helper\Config;
use Magento\Framework\View\Element\Template;
use Trans\AllowLocation\Helper\Data;

class Listing extends Template
{
    /**
     * @var array|LayoutProcessorInterface[]
     */
    protected $layoutProcessors;

    public function __construct(
        Template\Context $context,
        array $layoutProcessors = [],
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout']) ? $data['jsLayout'] : [];
        $this->layoutProcessors = $layoutProcessors;

    }

    public function getJsLayout()
    {
        foreach ($this->layoutProcessors as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout);
        }
        return \Zend_Json::encode($this->jsLayout);
    }

    public function getGoogleApiKey()
    {
        return $this->_scopeConfig->getValue(Data::XML_SECRET_KEY) ?? null;
    }

}
