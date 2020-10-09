<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Controller\Adminhtml;

/**
 * Class Category
 * @package SM\InspireMe\Controller\Adminhtml
 */
abstract class Category extends \Mirasvit\Blog\Controller\Adminhtml\Category
{
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Mirasvit_Blog::blog');
        $resultPage->getConfig()->getTitle()->prepend(__('Inspire Me'));
        $resultPage->getConfig()->getTitle()->prepend(__('Topics'));

        return $resultPage;
    }
}
