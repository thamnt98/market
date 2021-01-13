<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: June, 13 2020
 * Time: 5:25 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Cron;

class AbandonedCart extends AbstractGenerate
{
    const EVENT_NAME = 'abandoned_cart';

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * AbandonedCartOneHour constructor.
     *
     * @param \Magento\Framework\Filesystem                     $filesystem
     * @param \Magento\Store\Model\App\Emulation                $emulation
     * @param \Magento\Catalog\Api\ProductRepositoryInterface   $productRepository
     * @param \SM\Notification\Model\EventSetting               $eventSetting
     * @param \SM\Notification\Helper\Config                    $configHelper
     * @param \SM\Notification\Model\NotificationFactory        $notificationFactory
     * @param \SM\Notification\Model\ResourceModel\Notification $notificationResource
     * @param \Magento\Framework\App\ResourceConnection         $resourceConnection
     * @param \Magento\Framework\Logger\Monolog|null            $logger
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\App\Emulation $emulation,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \SM\Notification\Model\EventSetting $eventSetting,
        \SM\Notification\Helper\Config $configHelper,
        \SM\Notification\Model\NotificationFactory $notificationFactory,
        \SM\Notification\Model\ResourceModel\Notification $notificationResource,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Logger\Monolog $logger = null
    ) {
        parent::__construct(
            $filesystem,
            $emulation,
            $eventSetting,
            $configHelper,
            $notificationFactory,
            $notificationResource,
            $resourceConnection,
            $logger
        );
        $this->productRepository = $productRepository;
    }

    /**
     * Cron run main function.
     */
    public function process()
    {
        foreach ($this->getAbandonedCart() as $item) {
            if (empty($item['customer_id']) || empty($item['product_id']) || empty($item['entity_id'])) {
                continue;
            }

            try {
                if ($this->createNotification($item)) {
                    $event = self::EVENT_NAME;
                    $this->connection->delete(
                        \SM\Notification\Model\ResourceModel\TriggerEvent::TABLE_NAME,
                        "event_id = '{$item['entity_id']}' AND event_type = 'quote' AND event_name = '{$event}'"
                    );
                    $this->connection->insert(
                        \SM\Notification\Model\ResourceModel\TriggerEvent::TABLE_NAME,
                        [
                            'event_id'   => $item['entity_id'],
                            'event_type' => 'quote',
                            'event_name' => $event,
                        ]
                    );
                }
            } catch (\Exception $e) {
                $this->logger->error(
                    "Notification Abandoned_Cart_One_Hour create failed: \n\t" . $e->getMessage(),
                    $e->getTrace()
                );
            }
        }
    }

    /**
     * @param array $rowData
     *
     * @return int
     * @throws \Magento\Framework\Exception\AlreadyExistsException|\Magento\Framework\Exception\NoSuchEntityException
     */
    protected function createNotification($rowData)
    {
        if (empty($rowData['customer_id']) || empty($rowData['product_id'])) {
            return null;
        }

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productRepository->getById($rowData['product_id'], false);
        $title = 'You left something...';
        $message = "The items in your cart are waiting. Don't leave them hanging, continue shopping.";

        /** @var \SM\Notification\Model\Notification $notification */
        $notification = $this->notificationFactory->create();
        $notification->setTitle($title)
            ->setPushTitle($title)
            ->setContent($message)
            ->setPushContent($message)
            ->setEvent(\SM\Notification\Model\Notification::EVENT_UPDATE)
            ->setSubEvent(\SM\Notification\Model\Notification::EVENT_PROMO_AND_EVENT)
            ->setCustomerIds([$rowData['customer_id']])
            ->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_CART);

        $this->eventSetting->init($rowData['customer_id'], \SM\Notification\Model\Notification::EVENT_UPDATE);
        if ($this->eventSetting->isPush()) {
            // Emulation store view
            $this->emulation->startEnvironmentEmulation(
                $rowData['store_id'],
                \Magento\Framework\App\Area::AREA_FRONTEND
            );

            $notification->setPushTitle(__($title)->__toString())
                ->setPushContent(__($message)->__toString());

            $this->emulation->stopEnvironmentEmulation(); // End Emulation
        }

        if ($product->getImage()) {
            $notification->setImage(
                $product->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) .
                'catalog/product' . $product->getImage()
            );
        }

        $this->notificationResource->save($notification);

        return $notification->getId();
    }

    /**
     * @return array
     */
    protected function getAbandonedCart()
    {
        $time = $this->configHelper->getAbandonedCartHour();
        $selectLatestDate =$this->connection->select();
        $selectLatestDate->from('quote_item', 'qty_updated_at')
            ->where('quote_id = q.entity_id')
            ->order('qty_updated_at DESC')
            ->limit(1);

        $selectWhere = $this->connection->select();
        $selectWhere->from(
            ['q' => 'quote'],
            ['max(q.entity_id)']
        )->joinInner(
            ['qi' => 'quote_item'],
            'q.entity_id = qi.quote_id',
            []
        )->joinInner(
            ['c' => 'customer_entity'],
            'q.customer_id = c.entity_id',
            []
        )->joinLeft(
            ['n' => \SM\Notification\Model\ResourceModel\TriggerEvent::TABLE_NAME],
            "q.entity_id = n.event_id AND n.event_name = '" . self::EVENT_NAME . "'",
            []
        )->where(
            'q.is_active = ?',
            1
        )->where(
            'q.customer_id IS NOT NULL'
        )->where(
            'n.id IS NULL ' .
            'OR (n.created_at < (' . $selectLatestDate->__toString() . '))'
        )->where(
            'current_timestamp() >= DATE_ADD((' . $selectLatestDate->__toString() . '), INTERVAL ' . $time . ' hour)'
        )->group('q.customer_id');

        $selectLatestAddedItem = $this->connection->select();
        $selectLatestAddedItem->from('quote_item', 'product_id')
            ->where('quote_id = q.entity_id')
            ->where('parent_item_id IS NULL')
            ->order('item_id DESC')
            ->limit(1);

        $select = $this->connection->select();
        $select->from(
            ['q' => 'quote'],
            []
        )->columns([
            'q.entity_id',
            'q.customer_id',
            'q.store_id',
            '(' . $selectLatestAddedItem->__toString() . ') as product_id'
        ])->where(
            'entity_id IN ?',
            $selectWhere
        )->limit(50);

        return $this->connection->fetchAssoc($select);
    }

    /**
     * @return string
     */
    protected function getLockFileName()
    {
        return 'sm_notification_abandoned_cart.lock';
    }
}
