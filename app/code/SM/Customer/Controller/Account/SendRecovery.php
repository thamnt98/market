<?php

namespace SM\Customer\Controller\Account;

use Magento\Framework\App\Action\Action;

class SendRecovery extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Customer\Model\AuthenticationInterface
     */
    protected $authentication;

    /**
     * @var \SM\Customer\Api\TransCustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * SendRecovery constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Customer\Model\AuthenticationInterface $authentication
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\AuthenticationInterface $authentication,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->authentication = $authentication;
        $this->customerRepository = $customerRepository;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $user = $this->getRequest()->getParam('user');
        $type = $this->getRequest()->getParam('type');
        $status = false;
        if ($user && $type) {
            try {
                $customer = $this->customerRepository->get($user);
                if ($this->authentication->isLocked($customer->getId())) {
                    $this->_eventManager->dispatch(
                        'send_recovery_email',
                        ['customer' => $customer, 'type' => $type]
                    );
                    $status = true;
                    $message = __('Success');
                } else {
                    $message = __('Customer is unlocked.');
                }

            } catch (\Exception $e) {
                $message = $e->getMessage();
            }
        } else {
            $message = __('Email invalid');
        }
        $resultJson->setData(['status'=> $status, 'message' => $message]);
        return $resultJson;
    }
}
