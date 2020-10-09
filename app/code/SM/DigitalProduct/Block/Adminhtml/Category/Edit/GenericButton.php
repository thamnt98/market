<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Block\Adminhtml\Category\Edit;


abstract class GenericButton
{

    protected $context;

    /**
     * @param \Magento\Framework\View\Element\UiComponent\Context $context
     */
    public function __construct(\Magento\Framework\View\Element\UiComponent\Context $context)
    {
        $this->context = $context;
    }

    /**
     * Return model ID
     *
     * @return int|null
     */
    public function getModelId()
    {
        return $this->context->getRequestParam('category_id');
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrl($route, $params);
    }
}

