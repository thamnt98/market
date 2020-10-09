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
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;

/**
 * Class ValidateWidget
 * @package SM\Customer\Controller\Verify
 */
class ValidateWidget extends \Magento\Framework\App\Action\Action implements HttpPostActionInterface
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * ValidateVerified constructor.
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        Context $context
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->customerSession = $customerSession;
        $this->currentCustomer = $currentCustomer;
        $this->httpContext = $httpContext;
    }

    /**
     * Get current customer
     *
     * Return stored customer or get it from session
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @since 102.0.1
     */
    public function getCustomer(): \Magento\Customer\Api\Data\CustomerInterface
    {
        return $this->currentCustomer->getCustomer();
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();

        if (!$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH)) {
            $resultJson->setData(false);
            return $resultJson;
        }

        if ($this->customerSession->getWidgetClosed() === true) {
            $resultJson->setData(false);
            return $resultJson;
        }

        $customer = $this->getCustomer();
        if ($customer->getCustomAttribute('is_verified_email')) {
            if ($customer->getCustomAttribute('is_verified_email')->getValue() == 1) {
                $resultJson->setData(false);
                return $resultJson;
            }
        }

        $resultJson->setData(true);
        return $resultJson;
    }
}
