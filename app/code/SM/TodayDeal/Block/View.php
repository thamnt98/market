<?php

/**
 * @category SM
 * @package SM_TodayDeal
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Chinhvd <chinhvd@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\TodayDeal\Block;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use SM\TodayDeal\Model\Post;
use SM\TodayDeal\Api\PostRepositoryInterface;

class View extends Template
{
    /**
     * Post
     * @var null
     */
    protected $_post = null;

    /**
     * PostFactory
     * @var PostRepositoryInterface
     */
    protected $postRepository;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Constructor
     * @param Context $context
     * @param PostRepositoryInterface $postRepository
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        PostRepositoryInterface $postRepository,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_filterProvider = $filterProvider;
        $this->postRepository = $postRepository;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Golbal Prepare Layout
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _prepareLayout()
    {
        $post = $this->getPost();
        $this->_addBreadcrumbs();
        $this->pageConfig->addBodyClass('cms-' . $post->getIdentifier());
        $metaTitle = $post->getMetaTitle();
        $this->pageConfig->getTitle()->set($metaTitle ? $metaTitle : $post->getTitle());
        $this->pageConfig->setKeywords($post->getMetaKeywords());
        $this->pageConfig->setDescription($post->getMetaDescription());

        return parent::_prepareLayout();
    }

    /**
     * Prepare breadcrumbs
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $breadcrumbsBlock->addCrumb(
            'home',
            [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link' => $baseUrl
            ]
        );
        $breadcrumbsBlock->addCrumb(
            'today_deals',
            [
                'label' => __("Curated For You"),
                'title' => __("Curated For You"),
                'link' => $baseUrl . '/curatedforyou'
            ]
        );

        $postTitle = $this->getPost()->getTitle();

        $breadcrumbsBlock->addCrumb(
            'post',
            [
                'label' => __($postTitle),
                'title' => __($postTitle),
            ]
        );
    }

    /**
     * Lazy loads the requested post
     * @return Post
     * @throws LocalizedException
     */
    public function getPost()
    {
        $id = $this->getRequest()->getParam('post_id');
        if ($this->_post === null) {
            /** @var Post $post */
            $post = $this->postRepository->getById($id);
            if (!$post->getId()) {
                throw new LocalizedException(__('Post not found'));
            }

            $this->_post = $post;
        }
        return $this->_post;
    }

    /**
     * Get content post
     * @throws LocalizedException
     * @throws \Exception
     */
    public function getContent()
    {
        return $this->_filterProvider->getPageFilter()->filter($this->getPost()->getContent());
    }
}
