<?php

namespace SM\ShoppingList\Block\MyList;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use SM\ShoppingList\Api\Data\ShoppingListDataInterface;
use SM\ShoppingList\Model\ShoppingListRepository;

/**
 * Class Listing
 * @package SM\ShoppingList\Block\MyList
 */
class Listing extends Template
{
    /**
     * @var ShoppingListRepository
     */
    protected $shoppingListRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;
    /**
     * @var Http
     */
    protected $response;
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * Listing constructor.
     * @param FilterBuilder $filterBuilder
     * @param ShoppingListRepository $shoppingListRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Context $context
     * @param CurrentCustomer $currentCustomer
     * @param Http $response
     * @param array $data
     */
    public function __construct(
        FilterBuilder $filterBuilder,
        ShoppingListRepository $shoppingListRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Context $context,
        CurrentCustomer $currentCustomer,
        Http $response,
        array $data = []
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->shoppingListRepository = $shoppingListRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->response = $response;
        parent::__construct($context, $data);
    }

    /**
     * @return Template
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Shopping List'));
        return parent::_prepareLayout();
    }

    /**
     * @return ShoppingListDataInterface[]
     * @throws LocalizedException
     */
    public function getShoppingLists()
    {
        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('shoppinglist');
        }

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchResults = $this->shoppingListRepository->getList(
            $searchCriteria,
            $this->currentCustomer->getCustomerId()
        );
        return $searchResults->getItems();
    }

    /**
     * @return string
     */
    public function getCreateListUrl()
    {
        return $this->getUrl("shoppinglist/ajax/createlist");
    }
}
