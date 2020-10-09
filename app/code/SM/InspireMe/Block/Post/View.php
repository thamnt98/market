<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Block\Post;

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Theme\Block\Html\Breadcrumbs;
use Magento\Theme\Block\Html\Title;
use Mirasvit\Blog\Model\Config;
use Mirasvit\Blog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

/**
 * Class View
 * @package SM\InspireMe\Block\Post
 */
class View extends \Mirasvit\Blog\Block\Post\View
{
    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * View constructor.
     * @param CategoryCollectionFactory $postCollectionFactory
     * @param Config $config
     * @param FilterProvider $filterProvider
     * @param Registry $registry
     * @param TimezoneInterface $timezone
     * @param Context $context
     */
    public function __construct(
        CategoryCollectionFactory $postCollectionFactory,
        Config $config,
        FilterProvider $filterProvider,
        Registry $registry,
        TimezoneInterface $timezone,
        Context $context
    ) {
        parent::__construct($postCollectionFactory, $config, $filterProvider, $registry, $context);
        $this->timezone = $timezone;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $post = $this->getPost();
        $title = $post ? $post->getName() : $this->config->getBlogName();

        $metaTitle = $post
            ? ($post->getMetaTitle() ? $post->getMetaTitle() : $post->getName())
            : $this->config->getBaseMetaTitle();

        $metaDescription = $post
            ? ($post->getMetaDescription() ? $post->getMetaDescription() : $post->getName())
            : $this->config->getBaseMetaDescription();

        $metaKeywords = $post
            ? ($post->getMetaKeywords() ? $post->getMetaKeywords() : $post->getName())
            : $this->config->getBaseMetaKeywords();

        $this->pageConfig->getTitle()->set($metaTitle);
        $this->pageConfig->setDescription($metaDescription);
        $this->pageConfig->setKeywords($metaKeywords);

        /** @var Breadcrumbs $breadcrumbs */
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link'  => $this->context->getUrlBuilder()->getBaseUrl(),
            ])->addCrumb('blog', [
                'label' => $this->config->getBlogName(),
                'title' => $this->config->getBlogName(),
                'link'  => $this->config->getBaseUrl(),
            ]);

            $breadcrumbs->addCrumb('postname', [
                'label' => $title,
                'title' => $title,
            ]);
        }

        return $this;
    }

    /**
     * @param $date
     * @return string
     */
    public function getFormatDate($date)
    {
        return $this->timezone->formatDateTime(
            $date,
            null,
            null,
            null,
            $this->timezone->getConfigTimezone(),
            'd LLL YYYY'
        );
    }

    /**
     * @return string
     */
    public function getPostContent()
    {
        return $this->filterProvider->getPageFilter()->filter($this->getPost()->getContent());
    }
}
