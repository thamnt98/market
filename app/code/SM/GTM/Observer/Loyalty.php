<?php

namespace SM\GTM\Observer;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\CustomerFactory;
use Magento\Customer\Model\SessionFactory as CustomerSessionFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as orderCollection;

class Loyalty implements ObserverInterface
{
    /**
     * @var orderCollection
     */
    private $orderCollection;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;
    /**
     * @var CustomerSession
     */
    private $customerSession;
    /**
     * @var CustomerFactory
     */
    private $customerFactory;
    /**
     * @var Customer
     */
    private $customer;
    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;
    /**
     * Loyalty constructor.
     * @param orderCollection $orderCollection
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param CustomerSessionFactory $customerSession
     * @param CustomerFactory $customerFactory
     * @param Customer $customer
     */
    public function __construct(
        orderCollection $orderCollection,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        CustomerSessionFactory $customerSession,
        CustomerFactory $customerFactory,
        Customer $customer,
        \Magento\Framework\App\State $state
    ) {
        $this->orderCollection = $orderCollection;
        $this->date = $date;
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        $this->customer = $customer;
        $this->state = $state;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $unaccessArea = [
            \Magento\Framework\App\Area::AREA_WEBAPI_REST,
            \Magento\Framework\App\Area::AREA_CRONTAB,
            \Magento\Framework\App\Area::AREA_WEBAPI_SOAP,
            \Magento\Framework\App\Area::AREA_GRAPHQL
        ];
        if (!in_array($this->state->getAreaCode(), $unaccessArea)) {
            $hasRule = true;
            $date = $this->date->gmtDate(null, strtotime('-1 month'));
            $orderCollection = $this->orderCollection->create();
            $orderGet = $observer->getEvent()->getOrder();
            $OrderStatus = $orderGet->getStatus();
            $customerOrderId = $orderGet->getCustomerId();

            $customerId = $this->customerSession->create()->getCustomerId();

            if ($customerId) {
                $customer = $this->customer->load($customerId);
                if ($customerOrderId != $customerId) {
                    return;
                }
            } else {
                $customerId = $customerOrderId;
                $customer = $this->customer->load($customerOrderId);
            }

            $customerData = $customer->getDataModel();
            $currentLoyalty = $customerData->getCustomAttribute('loyalty') ?
                $customerData->getCustomAttribute('loyalty')->getValue() : 'Lapsed';
            $loyalty = $currentLoyalty;
            if ($OrderStatus == 'complete') {
                if ($currentLoyalty == 'Active') {
                    $loyalty = 'Active';
                } else {
                    $orders = $orderCollection->addAttributeToFilter('customer_id', $customerId)
                        ->addAttributeToFilter('status', 'complete')
                        ->addAttributeToFilter('created_at', ['gteq' => $date]);
                    $size = $orders->getSize();
                    if ($currentLoyalty == 'Dormant') {
                        $loyalty = $size ? 'Active' : 'Dormant';
                    } else {
                        $items = $orderGet->getAllItems() ? $orderGet->getAllItems() : null;
                        foreach ($items as $item) {
                            if (!$item->getAppliedRuleIds()) {
                                $hasRule = false;
                                break;
                            }
                        }
                        if ($currentLoyalty == 'Non Loyal') {
                            if ($hasRule) {
                                $loyalty = 'Non Loyal';
                            } else {
                                if ($size) {
                                    $loyalty = 'Active';
                                } else {
                                    $loyalty = 'Dormant';
                                }
                            }
                        } elseif ($currentLoyalty == 'Lapsed') {
                            $loyalty = $hasRule ? "Non Loyal" : 'Active';
                        }
                    }
                }
            }
            $customerData->setCustomAttribute('loyalty', $loyalty);
            $customer->updateData($customerData);
            $customerResource = $this->customerFactory->create();
            $customerResource->saveAttribute($customer, 'loyalty');

        }
    }
}
