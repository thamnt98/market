<?php

namespace SM\GTM\Block\Html;

class Breadcrumbs extends \Magento\Theme\Block\Html\Breadcrumbs implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @return array
     */
    public function getCrumbs()
    {
        return $this->_crumbs;
    }
}
