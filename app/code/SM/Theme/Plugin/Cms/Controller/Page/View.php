<?php
/**
 * Class View
 * @package SM\Theme\Plugin\Cms\Controller\Page
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Theme\Plugin\Cms\Controller\Page;

use SM\Catalog\ViewModel\SubCategories;

class View
{
    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cache;

    /**
     * Request
     *
     * @return \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Cms\Model\PageRepository
     */
    protected $pageRepository;

    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * View constructor.
     *
     * @param \Magento\Cms\Model\PageRepository $pageRepository
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Cms\Model\PageRepository $pageRepository,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->cache = $cache;
        $this->request = $request;
        $this->pageRepository = $pageRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param \Magento\Cms\Controller\Page\View $subject
     * @param $result
     * @return mixed
     */
    public function afterExecute(
        \Magento\Cms\Controller\Page\View $subject,
        $result
    ) {
        if ($result instanceof \Magento\Framework\View\Result\Page) {
            $pageId = $this->getRequest()->getParam('page_id', $this->getRequest()->getParam('id', false));
            $blockId = $this->cache->load(SubCategories::CACHE_STATIC_BLOCK_PREFIX . $pageId);
            $staticBlock = $result->getLayout()->getBlock('landing.content.category.block');

            if ($staticBlock && $blockId) {
                $staticBlock->setBlockId($blockId);
            }

            //Add body class
            $pageLayout = $result->getConfig()->getPageLayout();
            if ($pageLayout == 'landing-page') {
                $result->getConfig()->addBodyClass('page-layout-2columns-left');
            }
        }

        return $result;
    }

    /**
     * @return \Magento\Framework\App\RequestInterface
     */
    protected function getRequest()
    {
        return $this->request;
    }
}
