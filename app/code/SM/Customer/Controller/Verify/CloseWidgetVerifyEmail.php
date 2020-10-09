<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Customer\Controller\Verify;

use Magento\Framework\App\Action\Context;

/**
 * Class CloseWidgetVerifyEmail
 * @package SM\Customer\Controller\Verify
 */
class CloseWidgetVerifyEmail extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * CloseWidgetVerifyEmail constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param Context $context
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        Context $context
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->customerSession->setWidgetClosed(true);
    }
}
