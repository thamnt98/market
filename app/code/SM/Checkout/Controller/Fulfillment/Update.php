<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 3/31/20
 * Time: 10:40 AM
 */

namespace SM\Checkout\Controller\Fulfillment;

use Magento\Framework\App\Action\Context;

class Update extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var \Magento\Customer\Model\Session|Session
     */
    protected $customerSession;

    /**
     * Update constructor.
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Customer\Model\Session $customerSession,
        Context $context
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->customerSession->setFulfillment(true);
        $resultJson = $this->jsonFactory->create();
        $resultJson->setData(true);
        return $resultJson;
    }
}
