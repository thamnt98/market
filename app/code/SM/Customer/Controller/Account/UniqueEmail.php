<?php

namespace SM\Customer\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use SM\Customer\Api\TransCustomerRepositoryInterface;

class UniqueEmail extends Action implements HttpPostActionInterface
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \SM\Customer\Api\TransCustomerRepositoryInterface
     */
    protected $transCustomer;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param TransCustomerRepositoryInterface $transCustomer
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \SM\Customer\Api\TransCustomerRepositoryInterface $transCustomer,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->transCustomer = $transCustomer;
        $this->customerRepository = $customerRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $email = $this->getRequest()->getParam('email');
        $customer = null;
        try {
            $customer = $this->customerRepository->get($email);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            // This mean there is no customer registered with email
        } catch (\Exception $e) {
            // System error
        }

        if ($customer) {
            $resultJson->setData('false');
        } else {
            $resultJson->setData('true');
        }

        return $resultJson;
    }
}
