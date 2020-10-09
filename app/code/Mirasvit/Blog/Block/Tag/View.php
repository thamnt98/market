<?php

namespace Mirasvit\Blog\Block\Tag;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Blog\Model\Config;
use Mirasvit\Blog\Model\Tag;

class View extends Template
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Context
     */
    protected $context;
    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Registry $registry
     * @param Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        Context $context,
        Config $config,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->context  = $context;

        parent::__construct($context, $data);
        $this->config = $config;
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $tag = $this->getTag();
        if (!$tag) {
            return $this;
        }

        $this->pageConfig->getTitle()->set(__('Tag: %1', $tag->getName()));

        if ($tag && ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs'))) {
            $breadcrumbs->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link'  => $this->context->getUrlBuilder()->getBaseUrl(),
            ]);

            $breadcrumbs->addCrumb('blog', [
                'label' => $this->config->getBlogName(),
                'title' => $this->config->getBlogName(),
            ]);
        }

        return $this;
    }

    /**
     * @return Tag
     */
    public function getTag()
    {
        return $this->registry->registry('current_blog_tag');
    }
}
