<?php

namespace SM\Catalog\Block\Product\Compare;

use Magento\Framework\View\Element\Template;

class CompareLink extends \Magento\Framework\View\Element\Template{

    protected $compare;

    public function __construct(Template\Context $context,
                                \Magento\Catalog\Helper\Product\Compare $compare,
                                array $data = [])
    {
        $this->compare = $compare;
        parent::__construct($context, $data);
    }

    public function getCompareUrl(){
        return $this->compare->getListUrl();
    }

    public function getCompareCount(){
        $items = $this->compare->getItemCollection();
        return $items->getSize();

    }
}