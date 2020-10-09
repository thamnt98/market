<?php
/**
 * @category SM
 * @package SM_Theme
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Chinhvd <chinhvd@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);

namespace SM\Theme\Observer\Adminhtml;

use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use SM\Catalog\ViewModel\SubCategories;

class CategorySaveAfter implements ObserverInterface
{
    /**
     * @inheritDoc
     */
    private $pageRepositoryInterface;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    private $cache;

    /**
     * Construct
     *
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param PageRepositoryInterface $pageRepositoryInterface
     */
    public function __construct(
        \Magento\Framework\App\CacheInterface $cache,
        PageRepositoryInterface $pageRepositoryInterface
    ) {
        $this->pageRepositoryInterface = $pageRepositoryInterface;
        $this->cache = $cache;
    }

    /**
     * Save landing page
     * @param Observer $observer
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        /**
         * @var $category \Magento\Catalog\Model\Category
         */
        $category = $observer->getCategory();
        $pageId = $category->getData('trans_landing_page');

        if (empty($pageId)) {
            $category = $category->getParentCategory();
            $pageId = $category->getData('trans_landing_page');
        }

        if ($pageId) {
            try {
                // Set Page Options
                $categoryId = $category->getId();
                $page = $this->pageRepositoryInterface->getById($pageId);
                $page->setData('is_landing_of_category', $categoryId);
                $page->setData('page_layout', 'landing-page');
                $page->setData('custom_theme', $category->getCustomDesign());
                $this->pageRepositoryInterface->save($page);
                // Remove Landing Page Cache
                $identifier = SubCategories::CACHE_SUB_CATEGORIES_PREFIX . $pageId;
                $identifierBlock = SubCategories::CACHE_STATIC_BLOCK_PREFIX . $pageId;
                $this->cache->remove($identifier);
                $this->cache->remove($identifierBlock);
            } catch (\Exception $e) {
            }
        }
    }
}
