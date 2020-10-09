<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCustomer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.co.id>
 *
 * Copyright © 2020 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCustomer\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class UpdateCustomer
 */
class UpdateCustomer implements ObserverInterface {

	/**
	 * @var \Trans\IntegrationCustomer\Api\IntegrationCustomerInterface
	 */
	protected $customerInterface;

	/**
	 * @var \Trans\IntegrationCustomer\Api\IntegrationCustomerCentralRepositoryInterface
	 */
	protected $cdbRepository;

	/**
	 * Register Customer Constructor
	 * @param \Trans\IntegrationCustomer\Api\IntegrationCustomerCentralRepositoryInterface $cdbRepository
	 * @param \Trans\IntegrationCustomer\Api\IntegrationCustomerInterface $customerInterface
	 */
	public function __construct(
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
		\Trans\IntegrationCustomer\Api\IntegrationCustomerCentralRepositoryInterface $cdbRepository,
		\Trans\IntegrationCustomer\Api\IntegrationCustomerInterface $customerInterface
	) {
		$this->customerRepository = $customerRepository;
		$this->cdbRepository      = $cdbRepository;
		$this->customerInterface  = $customerInterface;

		$writer       = new \Zend\Log\Writer\Stream(BP . '/var/log/integration_customer.log');
		$logger       = new \Zend\Log\Logger();
		$this->logger = $logger->addWriter($writer);
	}

	/**
	 * Execute send update customer to CDB
	 *
	 * @param \Magento\Framework\Event\Observer $observer
	 * @return void
	 */
	public function execute(\Magento\Framework\Event\Observer $observer) {

		$this->logger->info('----------------------- Run Observer CDB ' . __CLASS__ . ' -------------------------');
		try {
			$customerEmail = $observer->getEvent()->getEmail();
			$customerData  = $this->cdbRepository->getCustomerByEmail($customerEmail);
			$customerId    = $customerData->getId();
			$customer      = $this->customerRepository->getById($customerId);

			$sendCustomer = $this->customerInterface->updateCustomerIntegration($customer);
		} catch (NoSuchEntityException $e) {
			$this->logger->info('error');
			$this->logger->info($e->getMessage());
		}
		$this->logger->info('----------------------- End Observer ' . __CLASS__ . ' -------------------------');
	}
}
