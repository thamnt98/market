<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: June, 11 2020
 * Time: 3:40 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Cron;

class LimitStock extends AbstractGenerate
{
    const EVENT_NAME = 'cart_item_limit_stock';

    /**
     * @var int
     */
    protected $labelId;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * LimitStock constructor.
     *
     * @param \Magento\Framework\Filesystem                     $filesystem
     * @param \Magento\Catalog\Model\ProductRepository          $productRepository
     * @param \Magento\Store\Model\App\Emulation                $emulation
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
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Store\Model\App\Emulation $emulation,
        \SM\Notification\Model\EventSetting $eventSetting,
        \SM\Notification\Helper\Config $configHelper,
        \SM\Notification\Model\NotificationFactory $notificationFactory,
        \SM\Notification\Model\ResourceModel\Notification $notificationResource,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Logger\Monolog $logger
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
     * @override
     */
    protected function construct()
    {
        parent::construct();
        // Get limited stock label id
        $this->labelId = $this->getLimitStockLabel();
    }

    public function process()
    {
        $items = $this->getActiveItems();
        foreach ($items as $item) {
            if (empty($item['customer_id']) ||
                empty($item['item_id']) ||
                empty($item['product_id']) ||
                !isset($item['store_id'])
            ) {
                continue;
            }

            try {
                if ($this->createNotification($item)) {
                    $this->connection->insert(
                        \SM\Notification\Model\ResourceModel\TriggerEvent::TABLE_NAME,
                        [
                            'event_id'   => $item['item_id'],
                            'event_type' => 'quote_item',
                            'event_name' => self::EVENT_NAME
                        ]
                    );
                }
            } catch (\Exception $e) {
                $this->logger->error(
                    "Notification Limit Stock create failed: \n\t" . $e->getMessage(),
                    $e->getTrace()
                );
            }
        }
    }

    /**
     * @param array $data
     *
     * @return int|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    protected function createNotification($data)
    {
        if (empty($data['product_id']) || empty($data['customer_id'])) {
            return null;
        }

        $product = $this->productRepository->getById($data['product_id']);
        if (!$product) {
            return null;
        }

        $title = '%1 is running out of stock!';
        $message = "Better put it in your cart now. Don't miss it.";
        $params = [
            'title' => $product->getName()
        ];
        /** @var \SM\Notification\Model\Notification $notification */
        $notification = $this->notificationFactory->create();
        $notification->setTitle($title)
            ->setContent($message)
            ->setEvent(\SM\Notification\Model\Notification::EVENT_UPDATE)
            ->setSubEvent(\SM\Notification\Model\Notification::EVENT_PROMO_AND_EVENT)
            ->setCustomerIds([$data['customer_id']])
            ->setRedirectType(\SM\Notification\Model\Source\RedirectType::TYPE_PDP)
            ->setRedirectId($product->getSku())
            ->setParams($params);

        if (isset($data['store_id'])) {
            // Emulation store view
            $this->emulation->startEnvironmentEmulation(
                $data['store_id'],
                \Magento\Framework\App\Area::AREA_FRONTEND
            );

            $notification->setPushTitle(__($title, $params['title'])->__toString())
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
    protected function getActiveItems()
    {
        $select = $this->connection->select();
        $select->from(
            ['i' => 'quote_item'],
            ['item_id', 'product_id', 'store_id', 'name']
        )->joinInner(
            ['q' => 'quote'],
            'i.quote_id = q.entity_id',
            ['customer_id']
        )->joinInner(
            ['c' => 'customer_entity'],
            'q.customer_id = c.entity_id',
            []
        )->joinLeft(
            ['n' => \SM\Notification\Model\ResourceModel\TriggerEvent::TABLE_NAME],
            'i.item_id = n.event_id',
            []
        )->joinLeft(
            ['l' => \Amasty\Label\Model\ResourceModel\Index::AMASTY_LABEL_INDEX_TABLE],
            'l.product_id = i.product_id AND l.store_id = i.store_id AND l.label_id = ' . $this->labelId,
            []
        )->where(
            'l.index_id IS NOT NULL'
        )->where(
            'q.is_active = ?',
            1
        )->where(
            'q.customer_id IS NOT NULL'
        )->where(
            'n.event_id IS NULL OR n.event_name <> ?',
            self::EVENT_NAME
        );

        return $this->connection->fetchAssoc($select);
    }

    /**
     * @return int
     */
    protected function getLimitStockLabel()
    {
        $select = $this->connection->select();
        $select->from('am_label', 'label_id')
            ->where('type_label_stock = ?', 'limited-stock')
            ->order('label_id DESC')
            ->limit(1);

        return (int)$this->connection->fetchOne($select);
    }

    /**
     * @return string
     */
    protected function getLockFileName()
    {
        return 'sm_notification_limit_stock.lock';
    }
}
