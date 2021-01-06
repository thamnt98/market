<?php
/**
 * Class RequestSuccess
 * @package SM\Sales\Controller\CreditMemo
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Sales\Controller\CreditMemo;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

class RequestSuccess extends Action
{
    public function execute(): \Magento\Framework\View\Result\Page
    {
        /**
         * @var \Magento\Framework\View\Result\Page $resultPage
         */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set(__('Refund Form Submitted'));
        return $resultPage;
    }
}
