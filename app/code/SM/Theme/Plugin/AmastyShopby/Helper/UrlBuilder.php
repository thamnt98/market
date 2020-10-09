<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Theme
 *
 * Date: May, 29 2020
 * Time: 5:34 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Theme\Plugin\AmastyShopby\Helper;

class UrlBuilder
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->registry = $registry;
        $this->request = $request;
    }

    public function afterBuildUrl(\Amasty\Shopby\Helper\UrlBuilder $subject, $result)
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $this->registry->registry('current_category');
        $actionName = $this->request->getFullActionName();
        if ($category && $actionName === 'cms_page_view') {
            $urlArr = explode('?', $result);
            $result = $category->getUrl() . '?' . ($urlArr[1] ?? '');
        }

        return $result;
    }
}
