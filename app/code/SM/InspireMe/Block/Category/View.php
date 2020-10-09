<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Block\Category;

use Magento\Theme\Block\Html\Breadcrumbs;
use Mirasvit\Blog\Model\Category;

/**
 * Class View
 * @package SM\InspireMe\Block\Category
 */
class View extends \Mirasvit\Blog\Block\Category\View
{
    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $category = $this->getCategory();

        $metaTitle = $category
            ? ($category->getMetaTitle() ? $category->getMetaTitle() : $category->getName())
            : $this->config->getBaseMetaTitle();

        $metaDescription = $category
            ? ($category->getMetaDescription() ? $category->getMetaDescription() : $category->getName())
            : $this->config->getBaseMetaDescription();

        $metaKeywords = $category
            ? ($category->getMetaKeywords() ? $category->getMetaKeywords() : $category->getName())
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
            ]);

            if ($category) {
                $breadcrumbs->addCrumb('blog', [
                    'label' => $this->config->getBlogName(),
                    'title' => $this->config->getBlogName(),
                    'link'  => $this->config->getBaseUrl(),
                ]);
                $ids = $category->getParentIds();

                $ids[]   = 0;
                $parents = $this->categoryCollectionFactory->create()
                    ->addFieldToFilter('entity_id', $ids)
                    ->addNameToSelect()
                    ->excludeRoot()
                    ->setOrder('level', 'asc');

                /** @var Category $cat */
                foreach ($parents as $cat) {
                    $breadcrumbs->addCrumb($cat->getId(), [
                        'label' => $cat->getName(),
                        'title' => $cat->getName(),
                        'link'  => $cat->getUrl(),
                    ]);
                }

                $breadcrumbs->addCrumb($category->getId(), [
                    'label' => $category->getName(),
                    'title' => $category->getName(),
                ]);
            } else {
                $breadcrumbs->addCrumb('blog', [
                    'label' => $this->config->getBlogName(),
                    'title' => $this->config->getBlogName(),
                ]);
            }
        }

        return $this;
    }
}
