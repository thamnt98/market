<?php
/**
 * Class SubCategories
 * @package SM\Catalog\ViewModel
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Catalog\ViewModel;

use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;

class SubCategories implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    const CACHE_SUB_CATEGORIES_PREFIX = 'subcategories_page_';
    const CACHE_STATIC_BLOCK_PREFIX = 'category_static_block_';
    const PATH = 'media/resized/';


    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    private $cache;

    /**
     * Request
     *
     * @return \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \SM\Theme\Helper\Data
     */
    protected $helper;

    /**
     * SubCategories constructor.
     * @param \SM\Theme\Helper\Data $helper
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \SM\Theme\Helper\Data $helper,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->helper   = $helper;
        $this->cache    = $cache;
        $this->request  = $request;
    }

    /**
     * @param null|\Magento\Catalog\Api\Data\CategoryInterface $category
     * @return mixed
     */
    public function getSubCategories($category = null)
    {

        if ($category) {
            $pageId = $category->getData('trans_landing_page');
            if (empty($pageId)) {
                $parentCategory = $category->getParentCategory();
                $pageId = $parentCategory->getData('trans_landing_page');
            }
        } else {
            $pageId = $this->getRequest()->getParam('page_id', $this->getRequest()->getParam('id', false));
        }

        if ($pageId) {
            $identifier = self::CACHE_SUB_CATEGORIES_PREFIX . $pageId;
            if ($data = $this->cache->load($identifier)) {
                return json_decode($this->cache->load($identifier));
            }
        }
        return false;
    }

    /**
     * @return \Magento\Framework\App\RequestInterface
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * @param $image
     * @param $width
     * @param $height
     * @return bool|string
     * @throws \Exception
     */
    public function getImageResize($image, $width = null, $height = null)
    {
        return $this->helper->getImageResize($image, $width, $height);
    }
}
