<?php

namespace SM\MobileApi\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use SM\MobileApi\Model\Data\HomepageMessage\GreetingMessageFactory;
use SM\MobileApi\Model\GreetingMessage\Filter;
use SM\MobileApi\Model\GreetingMessage\Listing;
use SM\MobileApi\Model\GreetingMessage\Resolver;

/**
 * Class GreetingMessage
 * @package SM\MobileApi\Model
 */
class HomepageMessage
{

    /**
     * @var Listing
     */
    protected $listing;

    /**
     * @var GreetingMessageModel
     */
    protected $greetingMessageFactory;

    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var Authorization\TokenUserContext
     */
    protected $tokenUserContext;

    /**
     * HomepageMessage constructor.
     * @param Listing $listing
     * @param GreetingMessageFactory $greetingMessageFactory
     * @param Resolver $resolver
     * @param Filter $filter
     * @param CustomerRepositoryInterface $customerRepository
     * @param Authorization\TokenUserContext $tokenUserContext
     */
    public function __construct(
        Listing $listing,
        GreetingMessageFactory $greetingMessageFactory,
        Resolver $resolver,
        Filter $filter,
        CustomerRepositoryInterface $customerRepository,
        \SM\MobileApi\Model\Authorization\TokenUserContext $tokenUserContext
    ) {
        $this->listing                = $listing;
        $this->greetingMessageFactory = $greetingMessageFactory;
        $this->resolver               = $resolver;
        $this->filter                 = $filter;
        $this->customerRepository     = $customerRepository;
        $this->tokenUserContext        = $tokenUserContext;
    }

    /**
     * Get greeting message in homepage
     * @return Data\HomepageMessage\GreetingMessage
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMessage()
    {
        $customerId = $this->tokenUserContext->getUserId();
        if ($customerId && $customerId != 0) {
            //Get customer information
            $customer = $this->customerRepository->getById($customerId);
            $fullName = $customer->getFirstname() . ' ' . $customer->getLastname();

            //Get message without time range logic
            $listingConfigMessage = $this->listing->getConfigMessage($fullName);
            $configMessage        = $this->resolver->resolveConfigMessage($listingConfigMessage);

            //Get message with time range logic
            $listingMessage = $this->listing->getList($fullName);
            $messageFilter  = $this->filter->filterMessage($listingMessage);

            //Init model and return result
            $greetingMessageModel = $this->greetingMessageFactory->create();

            //If no message match the condition
            if (empty($messageFilter)) {
                $greetingMessageModel->setConfig($configMessage);
                return $greetingMessageModel;
            }

            $greetingMessageModel->setMessage($messageFilter[Listing::MESSAGE]);
            $greetingMessageModel->setTimeRange($messageFilter[Listing::START_TIME] . '-' . $messageFilter[Listing::END_TIME]);
            $greetingMessageModel->setRedirectType($messageFilter[Listing::REDIRECT]);
            $greetingMessageModel->setConfig($configMessage);
        } else {
            $greetingMessageModel = $this->greetingMessageFactory->create();
        }
        return $greetingMessageModel;
    }
}
