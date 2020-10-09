<?php

namespace SM\GTM\Controller\Gtm;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use SM\GTM\Model\Data\Collectors\Customer;

/**
 * Class Customer
 * @package SM\GTM\Controller\Gtm
 */
class CustomerInfo extends Action
{
    const SUCCESS_RESONSE_CODE = '200';

    /**
     * @var Customer
     */
    private $customerCollector;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * CustomerInfo constructor.
     * @param CustomerRepositoryInterface $customerRepository
     * @param Customer $customerCollector
     * @param Context $context
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        Customer $customerCollector,
        Context $context
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerCollector = $customerCollector;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $customerIdentifier = $this->getRequest()->getParam('identifier');
        $collectedData = $this->customerCollector
            ->setCustomer($this->getCustomer($customerIdentifier))
            ->collect();

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setHttpResponseCode(self::SUCCESS_RESONSE_CODE);
        $resultJson->setData($collectedData);

        return $resultJson;
    }

    /**
     * @param string|null $customerIdentifier
     * @return CustomerInterface|null
     */
    private function getCustomer($customerIdentifier)
    {
        /** @var FilterBuilder $filterBuilder */
        $filterBuilder = $this->_objectManager->create(FilterBuilder::class);
        /** @var FilterGroupBuilder $filterGroupsBuilder */
        $filterGroupsBuilder = $this->_objectManager->create(FilterGroupBuilder::class);

        $filterGroups = $filterGroupsBuilder->setFilters([
            $filterBuilder->setField('email')->setValue($customerIdentifier)->setConditionType('eq')->create(),
            $filterBuilder->setField('telephone')->setValue($customerIdentifier)->setConditionType('eq')->create(),
        ])->create();

        /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->_objectManager->create(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->setFilterGroups([$filterGroups])->setPageSize(1)->create();

        try {
            $customerList = $this->customerRepository->getList($searchCriteria);
            if ($customerList->getTotalCount()) {
                return $customerList->getItems()[0];
            }
        } catch (LocalizedException $localizedException) {
            // Skip that Exception.
        }

        return null;
    }
}
