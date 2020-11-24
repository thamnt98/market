<?php

namespace SM\Notification\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use SM\Notification\Model\Notification;

class EventProductBackToStock implements ObserverInterface
{
    /**
     * @var \SM\Notification\Model\NotificationFactory
     */
    protected $notificationFactory;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory
     */
    protected $quoteItemCollectionFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\InventoryCatalogAdminUi\Model\GetSourceItemsDataBySku
     */
    protected $sourceDataBySku;

    /**
     * @var \SM\Notification\Model\ResourceModel\Notification
     */
    protected $notificationResource;


    public function __construct(
        \SM\Notification\Model\NotificationFactory $notificationFactory,
        \SM\Notification\Model\ResourceModel\Notification $notificationResource,
        \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\InventoryCatalogAdminUi\Model\GetSourceItemsDataBySku $sourceDataBySku
    ) {
        $this->notificationFactory = $notificationFactory;
        $this->quoteItemCollectionFactory = $quoteItemCollectionFactory;
        $this->request = $request;
        $this->sourceDataBySku = $sourceDataBySku;
        $this->notificationResource = $notificationResource;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getEvent()->getDataObject();
        $sources = $this->request->getParam('sources');
        if (isset($sources) && isset($sources['assigned_sources']) && !empty($sources['assigned_sources'])) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/notification.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $dataSource = $this->sourceDataBySku->execute($product->getSku());
            $listCustomer = $this->getListCustomerId($product->getId());
            if ($this->checkInStock($sources['assigned_sources']) && !$this->checkInStock($dataSource) && !empty($listCustomer)) {
                /** @var Notification $notification */
                $notification = $this->notificationFactory->create();
                $notification->setTitle('%1 is back in stock')
                    ->setEvent(Notification::EVENT_UPDATE)
                    ->setSubEvent(\SM\Notification\Model\Notification::EVENT_INFO)
                    ->setCustomerIds($listCustomer)
                    ->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_PDP)
                    ->setRedirectId($product->getSku())
                    ->setContent("Let's hop and shop! Make sure you don't miss it this time.")
                    ->setPushContent(__("Let's hop and shop! Make sure you don't miss it this time."))
                    ->setPushTitle(__('%1 is back in stock', $product->getName()))
                    ->setParams([
                        'title' => [
                            $product->getName()
                        ]
                    ]);

                try {
                    $this->notificationResource->save($notification);
                } catch (\Exception $e) {
                    $logger->err('Cannot save notification message: ' . $e->getMessage());
                }
            }
        }
    }

    public function checkInStock($stockData)
    {
        $available = false;
        foreach ($stockData as $stock) {
            if ($stock['quantity'] > 0 && $stock['status'] == '1') {
                $available = true;
            }
        }

        return $available;
    }

    public function getListCustomerId($product_id)
    {
        $customers = [];
        /** @var \Magento\Quote\Model\ResourceModel\Quote\Item\Collection $quoteItemCollection */
        $quoteItemCollection = $this->quoteItemCollectionFactory->create();
        $quoteItemCollection->getSelect()
            ->joinInner('quote', 'main_table.quote_id = quote.entity_id', ['customer_id'])
            ->where('main_table.product_id=?', $product_id);
        foreach ($quoteItemCollection as $item) {
            if ($item->getCustomerId()) {
                $customers[$item->getCustomerId()] = $item->getCustomerId();
                array_unique($customers);
            }
        }

        return $customers;
    }
}
