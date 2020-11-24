<?php

namespace SM\Sales\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Data extends AbstractHelper
{
    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Inventory\Model\SourceRepository
     */
    protected $sourceRepository;

    /**
     * @var \Trans\IntegrationOrder\Helper\Config
     */
    protected $tranConfig;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $orderRepository;

    /**
     * Data constructor.
     *
     * @param \Magento\Sales\Model\OrderRepository      $orderRepository
     * @param \Trans\IntegrationOrder\Helper\Config     $tranConfig
     * @param \Magento\Inventory\Model\SourceRepository $sourceRepository
     * @param TimezoneInterface                         $timezone
     * @param Context                                   $context
     */
    public function __construct(
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Trans\IntegrationOrder\Helper\Config $tranConfig,
        \Magento\Inventory\Model\SourceRepository $sourceRepository,
        TimezoneInterface $timezone,
        Context $context
    ) {
        $this->timezone = $timezone;
        parent::__construct($context);
        $this->sourceRepository = $sourceRepository;
        $this->tranConfig = $tranConfig;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param string $time
     * @return string
     */
    public function timeFormat($time)
    {
        return $this->timezone->date(strtotime($time))->format('d M Y | H:i A');
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return \Magento\InventoryApi\Api\Data\SourceInterface|null
     */
    public function getOrderStorePickup($order)
    {
        if ($sourceId = $order->getData('store_pick_up')) {
            try {
                return $this->sourceRepository->get($sourceId);
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function getDeliveredStatus()
    {
        $result = $this->tranConfig->getDeliveredOrderStatus();
        if (empty($result)) {
            $result = 'delivered';
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getInDeliveryStatus()
    {
        $result = $this->tranConfig->getInDeliveryOrderStatus();
        if (empty($result)) {
            $result = 'in_delivery';
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getInProcessStatus()
    {
        $result = $this->tranConfig->getInProcessOrderStatus();
        if (empty($result)) {
            $result = 'in_process';
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getReadyToPickupStatus()
    {
        $result = $this->tranConfig->getPickupByCustomerStatus();
        if (empty($result)) {
            $result = 'pick_up_by_customer';
        }

        return $result;
    }

    /**
     * @param $id
     *
     * @return \Magento\Sales\Api\Data\OrderInterface|null
     */
    public function getOrderById($id)
    {
        try {
            return $this->orderRepository->get($id);
        } catch (\Exception $e) {
            return null;
        }
    }
}
