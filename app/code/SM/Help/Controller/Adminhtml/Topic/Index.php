<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Controller\Adminhtml\Topic;

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Index
 * @package SM\Help\Controller\Adminhtml\Topic
 */
class Index extends \SM\Help\Controller\Adminhtml\Topic
{
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->context->getResultFactory()->create(ResultFactory::TYPE_PAGE);

        $this->initPage($resultPage)
            ->getConfig()
            ->getTitle()
            ->prepend(__('All Topics'));

        return $resultPage;
    }
}
