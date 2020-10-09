<?php


namespace SM\Promotion\Cron;

class ProcessOrderRewardRule extends \SM\Notification\Cron\AbstractGenerate
{

    const REWARD_PENDING = 0;
    const REWARD_COMPLETED = 1;
    const REWARD_SKIP = 2;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var \Magento\SalesRule\Model\Service\CouponManagementService
     */
    protected $couponManagementService;

    /**
     * @var \Magento\SalesRule\Api\Data\CouponGenerationSpecInterfaceFactory|null
     */
    protected $generationSpecFactory;

    /**
     * @var \SM\Notification\Model\NotificationFactory
     */
    protected $notificationFactory;

    /**
     * ProcessOrderRewardRule constructor.
     *
     * @param \Magento\SalesRule\Api\Data\CouponGenerationSpecInterfaceFactory $generationSpecFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory       $collectionFactory
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory    $ruleCollectionFactory
     * @param \Magento\SalesRule\Model\Service\CouponManagementService         $couponManagementService
     * @param \Magento\Store\Model\App\Emulation                               $emulation
     * @param \SM\Notification\Helper\CustomerSetting                          $settingHelper
     * @param \SM\Notification\Model\NotificationFactory                       $notificationFactory
     * @param \SM\Notification\Model\ResourceModel\Notification                $notificationResource
     * @param \Magento\Framework\App\ResourceConnection                        $resourceConnection
     * @param \Magento\Framework\Logger\Monolog|null                           $logger
     */
    public function __construct(
        \Magento\SalesRule\Api\Data\CouponGenerationSpecInterfaceFactory $generationSpecFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Magento\SalesRule\Model\Service\CouponManagementService $couponManagementService,
        \Magento\Store\Model\App\Emulation $emulation,
        \SM\Notification\Helper\CustomerSetting $settingHelper,
        \SM\Notification\Model\NotificationFactory $notificationFactory,
        \SM\Notification\Model\ResourceModel\Notification $notificationResource,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Logger\Monolog $logger
    ) {
        parent::__construct(
            $emulation,
            $settingHelper,
            $notificationFactory,
            $notificationResource,
            $resourceConnection,
            $logger
        );
        $this->generationSpecFactory = $generationSpecFactory;
        $this->collectionFactory = $collectionFactory;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->couponManagementService = $couponManagementService;
    }

    public function execute()
    {
        try {
            $orderSkip = [];
            $orderRewarded = [];
            $orderError = [];
            $collection = $this->getCollection();
            $ruleIds = [];

            $resource = $collection->getResource();
            // Pre process & collect ruleID to load one time only
            /** @var \Magento\Sales\Model\Order $order */
            foreach ($collection as $order) {
                $appliedRules = $order->getAppliedRuleIds();
                if (empty($order->getCustomerId())) {
                    $order->setData('voucher_rewarded', self::REWARD_SKIP);
                    $orderSkip[] = $order->getId();
                    continue;
                }
                $appliedRules = explode(',', $appliedRules);
                $ruleIds = array_merge($ruleIds, $appliedRules);
            }

            //Load all rule by ids
            $ruleIds = array_unique($ruleIds);
            $ruleCollection = $this->getRuleCollection($ruleIds);

            /** @var \Magento\Sales\Model\Order $order */
            foreach ($collection as $order) {
                try {
                    $skipFlag = true;
                    if ($order->getData('voucher_rewarded') == self::REWARD_SKIP) {
                        continue;
                    }

                    $appliedRules = explode(',', $order->getAppliedRuleIds());
                    foreach ($appliedRules as $ruleId) {
                        $rule = $ruleCollection->getItemById($ruleId);
                        if (empty($rule)) {
                            continue;
                        }
                        if (empty($rule->getData('voucher_reward')) || empty($rule->getData('reward_rule'))) {
                            continue;
                        }

                        $skipFlag = false;
                        $couponData = [
                            'rule_id' => $rule->getData('reward_rule'),
                            'quantity' => 1,
                            'length' => 12,
                            'format' => 'alphanum',
                            'prefix' => '',
                            'suffix' => '',
                            'dash' => 0
                        ];
                        $couponSpec = $this->generationSpecFactory->create(['data' => $couponData]);
                        $couponSpec->getExtensionAttributes()->setCustomerId($order->getCustomerId());
                        $this->couponManagementService->generate($couponSpec);
                        $this->sendNotification($order);
                        $orderRewarded[] = $order->getId();
                    }

                    if ($skipFlag) {
                        $orderSkip[] = $order->getId();
                    }
                } catch (\Exception $ex) {
                    $orderError[] = ['id' => $order->getId(), 'message' => $ex->getMessage()];
                    continue;
                }
            }

            // Update order status
            foreach ($orderSkip as $orderId) {
                $order = $collection->getItemById($orderId);
                $order->setData('voucher_rewarded', self::REWARD_SKIP);
                $resource->save($order);
            }

            foreach ($orderRewarded as $orderId) {
                $order = $collection->getItemById($orderId);
                $order->setData('voucher_rewarded', self::REWARD_COMPLETED);
                $resource->save($order);
            }

            if (!empty($orderError)) {
                $this->logger->error('Reward Voucher error', $orderError);
            }
            $this->logger->info('Reward rule complete',
                ['Rewarded' => count($orderRewarded), 'Skip' => count($orderSkip)]);
        } catch (\Exception $ex) {
            $this->logger->emergency($ex->getMessage(), $ex->getTrace());
        }
    }

    public function getCollection()
    {
        $limit = 500;
        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('state', \Magento\Sales\Model\Order::STATE_COMPLETE)
            ->addFieldToFilter('applied_rule_ids', array('notnull' => true))
            ->addFieldToFilter('is_parent', 1)
            ->addFieldToFilter('voucher_rewarded', array('nin' => [self::REWARD_COMPLETED, self::REWARD_SKIP]))
            ->setPageSize($limit);

        return $collection;
    }

    /**
     * @param array $ids
     * @return \Magento\SalesRule\Model\ResourceModel\Rule\Collection
     */
    public function getRuleCollection($ids = [])
    {
        /** @var \Magento\SalesRule\Model\ResourceModel\Rule\Collection $collection */
        $collection = $this->ruleCollectionFactory->create();
        $collection->addFieldToFilter('rule_id', ['in' => [$ids]])
            ->addFieldToFilter('voucher_reward', 1)
            ->addFieldToFilter('reward_rule', ['notnull' => true])
            ->addFieldToFilter('reward_rule', ['neq' => 0]);
        return $collection;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     */
    public function sendNotification($order)
    {
        $params = [
            'title' => [
                $order->getCustomerName()
            ]
        ];
        /** @var \SM\Notification\Model\Notification $notification */
        $notification = $this->notificationFactory->create();
        $notification->setTitle('%1, we have a voucher for you')
            ->setEvent(\SM\Notification\Model\Notification::EVENT_PROMO)
            ->setCustomerIds([$order->getCustomerId()])
            ->setContent('Check this one out and shop now!')
            ->setParams($params);

        // Emulation store view
        $this->emulation->startEnvironmentEmulation(
            $order->getStoreId(),
            \Magento\Framework\App\Area::AREA_FRONTEND
        );
        $notification->setPushTitle(__('%1, we have a voucher for you', $params))
            ->setPushContent('Check this one out and shop now!')
            ->setSms($order->getCustomerName() . ', we have a voucher for you\r\nFind it on My Voucher!');
        $this->emulation->stopEnvironmentEmulation(); // End Emulation

        try {
            $this->notificationResource->save($notification);
        } catch (\Exception $e) {
            $this->logger->error($e);
        }
    }
}