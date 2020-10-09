<?php

namespace SM\Customer\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use SM\Customer\Api\TransCustomerRepositoryInterface;

class UniquePhone extends Action implements HttpPostActionInterface
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
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param TransCustomerRepositoryInterface $transCustomer
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \SM\Customer\Api\TransCustomerRepositoryInterface $transCustomer
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->transCustomer = $transCustomer;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $phone = $this->getRequest()->getParam('telephone');
        $customer = null;
        try {
            $customer = $this->transCustomer->getByPhone($phone);
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
