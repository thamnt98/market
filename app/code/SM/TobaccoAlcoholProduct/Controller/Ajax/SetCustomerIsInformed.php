<?php
/**
 * SM\TobaccoAlcoholProduct\Controller
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\TobaccoAlcoholProduct\Controller\Ajax;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\InputMismatchException;

/**
 * Class SetCustomerIsInformed
 * @package SM\TobaccoAlcoholProduct\Controller
 */
class SetCustomerIsInformed extends Action
{
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var Session
     */
    protected $session;

    /**
     * SetCustomerIsInformed constructor.
     * @param Context $context
     * @param CurrentCustomer $currentCustomer
     * @param CustomerRepository $customerRepository
     * @param Session $session
     */
    public function __construct(
        Context $context,
        CurrentCustomer $currentCustomer,
        CustomerRepository $customerRepository,
        Session $session
    ) {
        $this->session = $session;
        $this->customerRepository = $customerRepository;
        $this->currentCustomer = $currentCustomer;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $data = ['response' => false];
        if ($this->session->isLoggedIn()) {
            $customer = $this->session->getCustomer();
            $customer->setData("is_alcohol_informed", 1);
            try {
                $this->customerRepository->save($customer->getDataModel());
                $data = ['response' => true];
            } catch (InputException|InputMismatchException|LocalizedException $e) {
                $data = ['response' => false, "message" => $e->getMessage()];
            }
        }
        $resultJson->setData($data);
        return $resultJson;
    }
}
