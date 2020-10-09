<?php

namespace SM\Notification\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use SM\Notification\Model\Notification;

class EventProductInShoppingListOnSale implements ObserverInterface
{
    /**
     * @var \SM\Notification\Model\NotificationFactory
     */
    protected $notificationFactory;

    /**
     * @var \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory
     */
    protected $wishlistCollectionFactory;

    /**
     * @var \SM\Notification\Model\ResourceModel\Notification
     */
    protected $notificationResource;

    /**
     * EventProductInShoppingListOnSale constructor.
     *
     * @param \SM\Notification\Model\NotificationFactory                       $notificationFactory
     * @param \SM\Notification\Model\ResourceModel\Notification                $notificationResource
     * @param \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $wishlistCollectionFactory
     */
    public function __construct(
        \SM\Notification\Model\NotificationFactory $notificationFactory,
        \SM\Notification\Model\ResourceModel\Notification $notificationResource,
        \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $wishlistCollectionFactory
    ) {
        $this->notificationFactory = $notificationFactory;
        $this->wishlistCollectionFactory = $wishlistCollectionFactory;
        $this->notificationResource = $notificationResource;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getProduct();
        if ($product->getFinalPrice() < $product->getPrice()) {
            $percentOff = round(($product->getPrice() - $product->getFinalPrice()) / $product->getPrice() * 100);
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/notification.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            /** @var \Magento\Wishlist\Model\ResourceModel\Wishlist\Collection $wishlistCollection */
            $wishlistCollection = $this->wishlistCollectionFactory->create();
            $wishlistCollection->getSelect()->joinInner(
                'wishlist_item',
                'wishlist_item.wishlist_id = main_table.wishlist_id',
                ['product_id']
            );
            $wishlistCollection->addFieldToFilter('product_id', $product->getId());
            $listCustomer = [];
            foreach ($wishlistCollection as $wishlist) {
                $listCustomer[] = $wishlist->getCustomerId();
                $listCustomer = array_unique($listCustomer);
            }

            /** @var Notification $notification */
            $notification = $this->notificationFactory->create();
            $params = [
                'title' => [
                    $product->getName(),
                    $percentOff
                ]
            ];
            $notification->setTitle('%1 is now %2% OFF')
                ->setPushTitle(__('%1 is now %2% OFF', $params['title']))
                ->setContent("Let's get it! Only in limited time and stock.")
                ->setPushContent(__("Let's get it! Only in limited time and stock."))
                ->setCustomerIds($listCustomer)
                ->setEvent(Notification::EVENT_UPDATE)
                ->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_PDP)
                ->setRedirectId($product->getSku())
                ->setParams($params);

            try {
                $this->notificationResource->save($notification);
            } catch (\Exception $e) {
                $logger->info('Cannot save notification: ' . $e->getMessage());
            }
        }
    }
}
