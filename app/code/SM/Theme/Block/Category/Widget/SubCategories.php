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

namespace SM\Theme\Block\Category\Widget;

use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Widget\Block\BlockInterface;

class SubCategories extends \Magento\Framework\View\Element\Template implements BlockInterface
{
    const CACHE_SUB_CATEGORIES_PREFIX = 'subcategories_page_';

    /**
     * @var \SM\Theme\Helper\Data
     */
    protected $helper;

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
     * SubCategories constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \SM\Theme\Helper\Data $helper
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \SM\Theme\Helper\Data $helper,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->helper       = $helper;
        $this->cache        = $cache;
        $this->request      = $request;
        parent::__construct($context, $data);
    }

    /**
     * @param null|\Magento\Catalog\Api\Data\CategoryInterface $category
     * @return mixed
     */
    public function getSubCategories($category = null)
    {
        if ($category) {
            $pageId = $category->getData('trans_landing_page');
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
    public function getRequest()
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
