<?php
/**
 * Class CatalogCategorySaveAfter
 * @package SM\Catalog\Observer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Catalog\Observer\Frontend;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use SM\Catalog\ViewModel\SubCategories;

class CatalogInitAfter implements ObserverInterface
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
     * Page constructor.
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\App\CacheInterface $cache
     */
    public function __construct(
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\App\CacheInterface $cache
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->cache = $cache;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $category = $observer->getCategory();

        if ($landingPageId = $category->getData('trans_landing_page')) {
            $identifier = SubCategories::CACHE_SUB_CATEGORIES_PREFIX . $landingPageId;
            $identifierBlock = SubCategories::CACHE_STATIC_BLOCK_PREFIX . $landingPageId;

            try {
                if (empty($this->cache->load($identifierBlock))) {
                    if ($staticBlockId = $category->getData('landing_page')) {
                        $this->cache->save($staticBlockId, $identifierBlock);
                    }
                }

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
