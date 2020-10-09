<?php

declare(strict_types=1);

namespace SM\Search\Model\SearchQueryPersonal;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Search\Model\Query;
use SM\Search\Api\Entity\SearchQueryPersonalInterface;
use SM\Search\Api\Repository\SearchQueryPersonalRepositoryInterface;
use SM\Search\Helper\Config;

class Updater
{
    /**
     * @var SearchQueryPersonalRepositoryInterface
     */
    protected $repository;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var bool
     */
    protected $isSaved = false;

    /**
     * Updater constructor.
     * @param SearchQueryPersonalRepositoryInterface $repository
     * @param CustomerSession $customerSession
     * @param RequestInterface $request
     */
    public function __construct(
        SearchQueryPersonalRepositoryInterface $repository,
        CustomerSession $customerSession,
        RequestInterface $request
    ) {
        $this->repository = $repository;
        $this->customerSession = $customerSession;
        $this->request = $request;
    }

    /**
     * @param Query $query
     * @throws LocalizedException
     */
    public function updateOnSearch(Query $query): void
    {
        $customerId = $query->getData(Config::CUSTOMER_ID_ATTRIBUTE_CODE) ?? $this->customerSession->getId();

        if (!$customerId) {
            return;
        }

        if ($this->isSaved) {
            return;
        }

        if ($query->getNumResults() == 0) {
            return;
        }

        if ($this->request->getParam(Config::SEARCH_PARAM_CATEGORY_FIELD_NAME)) {
            return;
        }

        $entityData = [
            SearchQueryPersonalInterface::STORE_ID => $query->getStoreId(),
            SearchQueryPersonalInterface::CUSTOMER_ID => $customerId,
            SearchQueryPersonalInterface::QUERY_TEXT => trim($query->getQueryText()),
        ];

        $this->repository->saveEntity($entityData);

        $this->isSaved = true;
    }
}
