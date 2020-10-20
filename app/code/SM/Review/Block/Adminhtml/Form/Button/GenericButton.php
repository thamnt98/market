<?php

namespace SM\Review\Block\Adminhtml\Form\Button;

use Magento\Backend\Block\Widget\Context;

/**
 * Class GenericButton
 * @package SM\Review\Block\Adminhtml\Form\Button
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
