<?php
/**
 * @category Magento
 * @package SM\Sales\Block\Order
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Block\Customer\Order;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use SM\Sales\Api\Data\ParentOrderDataInterface;
use SM\Sales\Api\Data\ParentOrderSearchResultsInterface;
use SM\Sales\Model\ParentOrderRepository;
use SM\Sales\Model\SubOrderRepository;

/**
 * Class InProgress
 * @package SM\Sales\Block\Customer\Order\Listing
 */
class Listing extends Template
{
    const TAB_IN_PROGRESS = "in-progress";
    const TAB_COMPLETED = "completed";

    const PHYSICAL_ORDER_TEMPLATE = "SM_Sales::customer/order/listing/item/physical.phtml";
    const DIGITAL_ORDER_TEMPLATE = "SM_Sales::customer/order/listing/item/digital.phtml";

    /**
     * @var SubOrderRepository
     */
    protected $subOrderRepository;
    /**
     * @var ParentOrderRepository
     */
    protected $parentOrderRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var SortOrderBuilder
     */
    protected $sortOrderBuilder;
    /**
     * @var FilterGroupBuilder
     */
    protected $filterGroupBuilder;
    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;
    /**
     * @var ParentOrderDataInterface[]
     */
    protected $orders;

    /**
     * Listing constructor.
     * @param Context $context
     * @param ParentOrderRepository $ParentOrderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param FilterBuilder $filterBuilder
     * @param CurrentCustomer $currentCustomer
     * @param SubOrderRepository $subOrderRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        ParentOrderRepository $ParentOrderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        FilterBuilder $filterBuilder,
        CurrentCustomer $currentCustomer,
        SubOrderRepository $subOrderRepository,
        array $data = []
    ) {
        $this->subOrderRepository = $subOrderRepository;
        $this->currentCustomer = $currentCustomer;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->parentOrderRepository = $ParentOrderRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return $this|Template
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $currentTab = $this->getRequest()->getParam("tab", self::TAB_IN_PROGRESS);
        $this->getOrderList($currentTab);

        if ($this->parentOrderRepository->getOrderCollection()) {

            /** @var \SM\Sales\Block\Customer\Order\Pager $pager */
            $pager = $this->getLayout()->createBlock(
                'SM\Sales\Block\Customer\Order\Pager',
                'order.pager'
            );
            $pager
                ->setLimit(ParentOrderRepository::DEFAULT_PAGE_SIZE)
                ->setShowPerPage(false)
                ->setCollection(
                    $this->parentOrderRepository->getOrderCollection()
                );
            $this->setChild('pager', $pager);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getStatuses()
    {
        $result = json_decode($this->subOrderRepository->getStatusLabel(), true);
        return is_array($result) ? $result : [];
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param string $tab
     */
    public function getOrderList($tab)
    {
        if ($customerId = $this->currentCustomer->getCustomerId()) {
            try {
                /** @var SearchCriteriaInterface $searchCriteria */
                $searchCriteria = $this->searchCriteriaBuilder->create();

                $sortOrder = $this->sort();

                $filterGroups = [];

                if ($tab == self::TAB_IN_PROGRESS) {
                    $filterGroups = $this->filterInProgress($filterGroups);
                } else {
                    $filterGroups = $this->filterCompleted($filterGroups);
                }

                $filterGroups = $this->search($filterGroups);
                $filterGroups = $this->filterTime($filterGroups);

                $searchCriteria->setFilterGroups($filterGroups);
                $searchCriteria->setSortOrders([$sortOrder]);

                $currentPage = $this->getRequest()->getParam("p", 1);
                $searchCriteria
                    ->setCurrentPage($currentPage)
                    ->setPageSize(ParentOrderRepository::DEFAULT_PAGE_SIZE);

                /** @var ParentOrderSearchResultsInterface $results */
                $results = $this->parentOrderRepository->getList($searchCriteria, $customerId);
                $this->setOrders($results->getItems());
            } catch (\Exception $e) {
                $this->setOrders([]);
            }
        } else {
            $this->setOrders([]);
        }
    }

    /**
     * @return SortOrder
     */
    public function sort()
    {
        /** @var SortOrder $sortOrder */
        $sortParam = $this->getRequest()->getParam(
            "sort",
            ParentOrderRepository::SORT_LATEST
        );

        if ($sortParam == ParentOrderRepository::SORT_STATUS) {
            return $this->sortOrderBuilder
                ->setField(ParentOrderRepository::SORT_STATUS)
                ->setDirection("desc")
                ->create();
        } else {
            return $this->sortOrderBuilder
                ->setField(ParentOrderRepository::SORT_LATEST)
                ->setDirection("desc")
                ->create();
        }
    }

    /**
     * @param array $filterGroups
     * @return array
     */
    public function filterTime($filterGroups)
    {
        if ($this->getRequest()->getParam("from", "") != "") {
            $from = $this->getRequest()->getParam("from");
            $filterGroups[] = $this->filterGroupBuilder
                ->addFilter($this->filterBuilder
                    ->setField("created_at")
                    ->setValue($from)
                    ->setConditionType("gteq")
                    ->create())
                ->create();
        }

        if ($this->getRequest()->getParam("to", "") != "") {
            $to = $this->getRequest()->getParam("to");
            $filterGroups[] = $this->filterGroupBuilder
                ->addFilter($this->filterBuilder
                    ->setField("created_at")
                    ->setValue($to)
                    ->setConditionType("lteq")
                    ->create())
                ->create();
        }
        return $filterGroups;
    }

    /**
     * @param array $filterGroups
     * @return array
     */
    public function search($filterGroups)
    {
        if ($this->getRequest()->getParam("key", "") != "") {
            $key = $this->getRequest()->getParam("key");

            $filterGroups[] = $this->filterGroupBuilder
                ->addFilter($this->filterBuilder
                    ->setField("name")
                    ->setValue($key)
                    ->setConditionType("like")
                    ->create())
                ->create();
        }

        return $filterGroups;
    }

    /**
     * @param array $filterGroups
     * @return array
     */
    public function filterInProgress($filterGroups)
    {
        $filterGroups[] = $this->filterGroupBuilder
            ->addFilter($this->filterBuilder
                ->setField(ParentOrderRepository::LIST_TYPE)
                ->setValue(ParentOrderRepository::IN_PROGRESS)
                ->create())
            ->create();
        return $filterGroups;
    }

    /**
     * @param array $filterGroups
     * @return array
     */
    public function filterCompleted($filterGroups)
    {
        $filterGroups[] = $this->filterGroupBuilder
            ->addFilter($this->filterBuilder
                ->setField(ParentOrderRepository::LIST_TYPE)
                ->setValue(ParentOrderRepository::COMPLETED)
                ->create())
            ->create();
        return $filterGroups;
    }

    /**
     * @return ParentOrderDataInterface[]
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param ParentOrderDataInterface[] $orders
     * @return $this
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
        return $this;
    }

    /**
     * @param \SM\Sales\Api\Data\DetailItemDataInterface $item
     * @param bool                                       $isShowNote
     * @return string
     */
    public function getInstallationHtml($item, $isShowNote = false)
    {
        try {
            /** @var \SM\Installation\Block\View $block */
            $block = $this->getLayout()->createBlock(
                \SM\Installation\Block\View::class,
                '',
                ['data' => ['item_id' => $item->getItemId(), 'show_note' => $isShowNote ? 'true' : 'false']]
            );
            $block->setInstallationData($item->getInstallationService());

            return $block->toHtml();
        } catch (\Exception $e) {
            return '';
        }
    }
}
