<?php
/**
 * Class CmsPageRender
 * @package SM\Theme\Observer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Theme\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use SM\Catalog\ViewModel\SubCategories;

class CmsPageRender implements ObserverInterface
{
    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    private $cache;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Page constructor.
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\Registry $registry
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->cache = $cache;
        $this->registry = $registry;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $page = $observer->getPage();
        if ($categoryId = $page->getData('is_landing_of_category')) {
            $identifier = SubCategories::CACHE_SUB_CATEGORIES_PREFIX . $page->getId();
            $identifierBlock = SubCategories::CACHE_STATIC_BLOCK_PREFIX . $page->getId();
            try {
                $category = $this->categoryRepository->get($categoryId);
                if (empty($this->cache->load($identifierBlock))) {
                    if ($staticBlockId = $category->getData('landing_page')) {
                        $this->cache->save($staticBlockId, $identifierBlock);
                    }
                }
                /**
                 *  Set current category on Landing page category.
                 */
                $this->registry->register('current_category', $category, true);

                if (empty($this->cache->load($identifier))) {
                    if ($subCategories = $category->getChildrenCategories()->getItems()) {
                        $data = [
                            'name' => $category->getName(),
                            'url' => $category->getUrl(),
                        ];

                        foreach ($subCategories as $category) {
                            $category = $this->categoryRepository->get($category->getId());
                            $data['children'][] = [
                                'name' => $category->getName(),
                                'thumbnail' => $category->getImageUrl('thumbnail'),
                                'url' => $category->getUrl()
                            ];
                        }
                        $this->cache->save(json_encode($data), $identifier);
                    }
                }
            } catch (\Exception $e) {
            }
        }
    }
}
