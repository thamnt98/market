<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Block\Sidebar;

/**
 * Class CategoryTree
 * @package SM\InspireMe\Block\Sidebar
 */
class CategoryTree extends \Mirasvit\Blog\Block\Sidebar\CategoryTree
{
    /**
     * @return \Mirasvit\Blog\Model\Category[]|\Mirasvit\Blog\Model\ResourceModel\Category\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTree()
    {
        return $this->categoryCollectionFactory->create()
            ->addAttributeToSelect(['name', 'url_key'])
            ->setOrder('name')
            ->addVisibilityFilter()
            ->excludeRoot();
    }

    /**
     * @return string
     */
    public function getIndexUrl()
    {
        return $this->_urlBuilder->getUrl('inspireme');
    }
}
