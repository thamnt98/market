<?php
/**
 * Class HandleOrderStatusHistory
 * @package SM\Sales\Model\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Sales\Model\Data;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\ResourceModel\Order\Status\Collection as StatusCollection;
use SM\Sales\Api\ParentOrderRepositoryInterface as Statuses;
use SM\Sales\Api\Data\StatusHistoryDataInterface;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory as StatusCollectionFactory;
use SM\Sales\Api\Data\StatusHistoryDataInterfaceFactory;

class HandleOrderStatusHistory
{
    const STORE_PICKUP_SHIPPING_CODE = 'store_pickup_store_pickup';
    const ICON_PATH = "images/svg-icons/icons-payment/";

    const SVG_PENDING_PAYMENT = "WaitingForPaymentGrey.svg";
    const SVG_ORDER_CANCELED = "WaitingForPaymentRed.svg";
    const SVG_IN_PROGRESS = "InProcessGrey.svg";
    const SVG_IN_DELIVERY = "InDeliveryGrey.svg";
    const SVG_COMPLETE = "OrderReceivedGrey.svg";
    const SVG_PICK_UP = "PaymentHandGrey.svg";
    const SVG_READY_PICK_UP = "PaymentHandGrey.svg";

    const MAP_STATUS_SVG = [
        Statuses::STATUS_PENDING_PAYMENT => self::SVG_PENDING_PAYMENT,
        Statuses::STATUS_ORDER_CANCELED => self::SVG_ORDER_CANCELED,
        Statuses::STATUS_IN_DELIVERY => self::SVG_IN_DELIVERY,
        Statuses::STATUS_FAILED_DELIVERY => self::SVG_IN_DELIVERY,
        Statuses::STATUS_IN_PROCESS => self::SVG_IN_PROGRESS,
        Statuses::STATUS_DELIVERED => self::SVG_COMPLETE,
        Statuses::STATUS_COMPLETE => self::SVG_COMPLETE,
        Statuses::IN_PROCESS_WAITING_FOR_PICKUP => self::SVG_IN_PROGRESS,
        Statuses::PICK_UP_BY_CUSTOMER => self::SVG_PICK_UP,
    ];

    const SVG_CLASS_PENDING_PAYMENT = "svg-icon-WaitingForPaymentGrey";
    const SVG_CLASS_IN_PROGRESS = "svg-icon-InProcessGrey";
    const SVG_CLASS_IN_DELIVERY = "svg-icon-InDeliveryGrey";
    const SVG_CLASS_COMPLETE = "svg-icon-OrderReceivedGrey";
    const SVG_CLASS_PICK_UP = "svg-icon-PickUpGrey";
    const SVG_CLASS_READY_PICK_UP = "svg-icon-ReadyPickUpGrey";

    const MAP_STATUS_SVG_CLASS = [
        Statuses::STATUS_PENDING_PAYMENT => self::SVG_CLASS_PENDING_PAYMENT,
        Statuses::STATUS_IN_DELIVERY => self::SVG_CLASS_IN_DELIVERY,
        Statuses::STATUS_FAILED_DELIVERY => self::SVG_CLASS_IN_DELIVERY,
        Statuses::STATUS_IN_PROCESS => self::SVG_CLASS_IN_PROGRESS,
        Statuses::STATUS_DELIVERED => self::SVG_CLASS_COMPLETE,
        Statuses::STATUS_COMPLETE => self::SVG_CLASS_COMPLETE,
        Statuses::IN_PROCESS_WAITING_FOR_PICKUP => self::SVG_CLASS_IN_PROGRESS,
        Statuses::PICK_UP_BY_CUSTOMER => self::SVG_CLASS_PICK_UP,
    ];

    const MAP_STATUS_WITH_LABEL = [
        Statuses::STATUS_PENDING_PAYMENT => 'Order Created. Waiting For Payment',
        Statuses::STATUS_ORDER_CANCELED => 'Order has been cancelled',
        Statuses::STATUS_IN_DELIVERY => 'Order is being Delivered by Courier',
        Statuses::STATUS_FAILED_DELIVERY => 'Your order has been cancelled due to unsuccessful delivery',
        Statuses::STATUS_IN_PROCESS => 'Payment Successful',
        Statuses::STATUS_DELIVERED => 'Order has been Successfully Delivered into Destination',
        Statuses::STATUS_COMPLETE => 'Order has been Successfully Completed',
        Statuses::IN_PROCESS_WAITING_FOR_PICKUP => 'Waiting to be picked up by Courier',
        Statuses::PICK_UP_BY_CUSTOMER => 'Waiting to be picked up by Customer',
        'payment_failed_order_canceled' => 'Payment Failed. Order will be cancelled',
        'transmart_order_canceled' => 'Order has been Cancelled by Transmart'
    ];

    const CANCEL_BY_PAYMENT = 1;
    const CANCEL_BY_DELIVERY = 2;
    const CANCEL_BY_PICKUP = 3;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var StatusHistoryDataInterfaceFactory
     */
    protected $statusHistoryDataFactory;

    /**
     * @var float|\Magento\Sales\Api\Data\OrderStatusHistoryInterface[]|null
     */
    private $histories;

    /**
     * @var \Magento\Sales\Model\Order
     */
    private $order;

    /**
     * @var array
     */
    private $statuses;

    /**
     * @var StatusCollectionFactory
     */
    protected $orderStatusCollectionFactory;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $viewRepository;

    /**
     * @var int
     */
    private $cancelType;

    /**
     * @var array
     */
    private $historyDetails;

    /**
     * HandleOrderStatusHistory constructor.
     * @param StatusCollectionFactory $orderStatusCollectionFactory
     * @param StatusHistoryDataInterfaceFactory $statusHistoryDataFactory
     * @param \Magento\Framework\View\Asset\Repository $viewRepository
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        StatusCollectionFactory $orderStatusCollectionFactory,
        StatusHistoryDataInterfaceFactory $statusHistoryDataFactory,
        \Magento\Framework\View\Asset\Repository $viewRepository,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        TimezoneInterface $timezone
    ) {
        $this->orderStatusCollectionFactory = $orderStatusCollectionFactory;
        $this->statusHistoryDataFactory = $statusHistoryDataFactory;
        $this->timezone = $timezone;
        $this->dateTime = $dateTime;
        $this->viewRepository = $viewRepository;
    }

    /**
     * @return array|StatusHistoryDataInterface[]
     */
    public function getStatusHistory()
    {
        $status = $this->order->getStatus();
        /** @var \Magento\Sales\Model\Order $order */
        if ($status == Statuses::STATUS_ORDER_CANCELED) {
            return $this->getStatusHistoryCanceled();
        } else {
            return $this->getStatusHistoryNormalCase();
        }
    }

    /**
     * @return array
     */
    public function getStatusHistoryDetails($order)
    {
        $items = [];
        $result = [];

        $this->histories = array_values($order->getStatusHistories());
        $this->order = $order;

        if (empty($this->historyDetails[$order->getId()])) {
            foreach ($this->histories as $history) {
                $items[$history->getStatus()] = $history;
            }
            $i = 0;
            if (isset($items[Statuses::STATUS_PENDING_PAYMENT])) {
                $result[$i] = $this->buildOrderHistoryDetail($items[Statuses::STATUS_PENDING_PAYMENT]);
                $i++;
            }
            if (isset($items[Statuses::STATUS_IN_PROCESS])) {
                $result[$i] = $this->buildOrderHistoryDetail($items[Statuses::STATUS_IN_PROCESS]);
                $i++;
            }
            if (isset($items[Statuses::IN_PROCESS_WAITING_FOR_PICKUP])) {
                $result[$i] = $this->buildOrderHistoryDetail($items[Statuses::IN_PROCESS_WAITING_FOR_PICKUP]);
                $i++;
            }
            if (isset($items[Statuses::PICK_UP_BY_CUSTOMER])) {
                $result[$i] = $this->buildOrderHistoryDetail($items[Statuses::PICK_UP_BY_CUSTOMER]);
                $i++;
            }
            if (isset($items[Statuses::STATUS_IN_DELIVERY])) {
                $result[$i] = $this->buildOrderHistoryDetail($items[Statuses::STATUS_IN_DELIVERY]);
                $i++;
            }
            if (isset($items[Statuses::STATUS_FAILED_DELIVERY])) {
                $result[$i] = $this->buildOrderHistoryDetail($items[Statuses::STATUS_FAILED_DELIVERY]);
                $i++;
            }
            if (isset($items[Statuses::STATUS_DELIVERED])) {
                $result[$i] = $this->buildOrderHistoryDetail($items[Statuses::STATUS_DELIVERED]);
                $i++;
            }
            if (isset($items[Statuses::STATUS_ORDER_CANCELED])) {
                $result[$i] = $this->buildOrderHistoryDetail($items[Statuses::STATUS_ORDER_CANCELED]);
                if (isset($result[$i - 1])) {
                    if ($result[$i - 1]->getStatus() == Statuses::STATUS_FAILED_DELIVERY) {
                        $result[$i]->setOrderUpdate(self::MAP_STATUS_WITH_LABEL['transmart_order_canceled']);
                    } elseif ($result[$i - 1]->getStatus() == Statuses::STATUS_PENDING_PAYMENT) {
                        $result[$i]->setOrderUpdate(self::MAP_STATUS_WITH_LABEL['payment_failed_order_canceled']);
                    }
                }
                $i++;
            }
            if (isset($items[Statuses::STATUS_COMPLETE])) {
                $result[$i] = $this->buildOrderHistoryDetail($items[Statuses::STATUS_COMPLETE]);
            }
            rsort($result);

            $this->historyDetails[$order->getId()] = $result;
        }
        return $this->historyDetails[$order->getId()];
    }

    /**
     * @param $history
     * @return StatusHistoryDataInterface
     */
    protected function buildOrderHistoryDetail($history)
    {
        $currentHistory = $this->statusHistoryDataFactory->create();
        $currentHistory->setData($history->getData());
        $currentHistory->setLabel($history->getStatusLabel());
        if (isset(self::MAP_STATUS_WITH_LABEL[$history->getStatus()])) {
            $currentHistory->setOrderUpdate(self::MAP_STATUS_WITH_LABEL[$history->getStatus()]);
        }

        $currentHistory->setCreatedAt($this->formatDate($history->getCreatedAt()));
        $currentHistory->setRawFormatDate($history->getCreatedAt());
        return $currentHistory;
    }

    /**
     * @return \SM\Sales\Api\Data\StatusHistoryDataInterface[]
     */
    private function getStatusHistoryNormalCase()
    {
        return [
            $this->getHistoryOne(),
            $this->getHistoryTwo(),
            $this->getHistoryThree(),
            $this->getHistoryFour(),
        ];
    }

    /**
     * @return array
     */
    public function getStatusHistoryCanceled()
    {
        $items = [];
        $historyDetails = $this->historyDetails[$this->order->getId()];
        $previousHistory = isset($historyDetails[1])
            ? $historyDetails[1]
            : null;
        $statusHistory = current($historyDetails);

        if ($previousHistory !== null) {
            $currentHistory = $this->statusHistoryDataFactory->create();
            $currentHistory->setData($statusHistory->getData());
            $currentHistory->setLabel($statusHistory->getStatusLabel());
            $currentHistory->setIsActive(true);

            switch ($previousHistory->getStatus()) {
                case Statuses::STATUS_PENDING_PAYMENT:
                    $currentHistory->setIcon($this->getCancelIcon(Statuses::STATUS_PENDING_PAYMENT));
                    $currentHistory->setIconClass($this->getCancelIconClass(Statuses::STATUS_PENDING_PAYMENT));
                    $items = [
                        $currentHistory,
                        $this->getHistoryTwo(),
                        $this->getHistoryThree(),
                        $this->getHistoryFour(),
                    ];
                    $this->cancelType = self::CANCEL_BY_PAYMENT;
                    break;
                case Statuses::STATUS_IN_PROCESS:
                    $currentHistory->setIcon($this->getCancelIcon(Statuses::STATUS_IN_PROCESS));
                    $currentHistory->setIconClass($this->getCancelIconClass(Statuses::STATUS_IN_PROCESS));
                    $items = [
                        $this->getHistoryOne(),
                        $currentHistory,
                        $this->getHistoryThree(),
                        $this->getHistoryFour(),
                    ];
                    break;
                case Statuses::PICK_UP_BY_CUSTOMER:
                    $currentHistory->setIcon($this->getCancelIcon(Statuses::PICK_UP_BY_CUSTOMER));
                    $currentHistory->setIconClass($this->getCancelIconClass(Statuses::PICK_UP_BY_CUSTOMER));
                    $items = [
                        $this->getHistoryOne(),
                        $this->getHistoryTwo(),
                        $currentHistory,
                        $this->getHistoryFour(),
                    ];
                    $this->cancelType = self::CANCEL_BY_PICKUP;
                    break;
                case Statuses::STATUS_IN_DELIVERY:
                case Statuses::STATUS_FAILED_DELIVERY:
                    $currentHistory->setIcon($this->getCancelIcon(Statuses::STATUS_IN_DELIVERY));
                    $currentHistory->setIconClass($this->getCancelIconClass(Statuses::STATUS_IN_DELIVERY));
                    $items = [
                        $this->getHistoryOne(),
                        $this->getHistoryTwo(),
                        $currentHistory,
                        $this->getHistoryFour(),
                    ];
                    $this->cancelType = self::CANCEL_BY_DELIVERY;
                    break;
                default:
                    $this->cancelType = self::CANCEL_BY_PAYMENT;
                    $items = [];
                    break;
            }
        }
        if (empty($this->cancelType)) {
            $this->cancelType = self::CANCEL_BY_PAYMENT;
        }
        return $items;
    }

    /**
     * @return \SM\Sales\Api\Data\StatusHistoryDataInterface
     */
    private function getHistoryOne()
    {
        $currentHistory = $this->statusHistoryDataFactory->create();
        $historyDetails = $this->historyDetails[$this->order->getId()];
        $history = $historyDetails[count($historyDetails) - 1];

        if ($history) {
            $currentHistory->setData([
                StatusHistoryDataInterface::STATUS => $history->getStatus(),
                StatusHistoryDataInterface::LABEL => $history->getLabel(),
                StatusHistoryDataInterface::CREATED_AT => $this->formatDate($history->getCreatedAt()),
                StatusHistoryDataInterface::ORDER_UPDATE => $history->getComment(),
                StatusHistoryDataInterface::ICON => $this->getActiveIcon($history->getStatus()),
                StatusHistoryDataInterface::ICON_CLASS => $this->getActiveIconClass($history->getStatus()),
                StatusHistoryDataInterface::IS_ACTIVE => true,
                StatusHistoryDataInterface::RAW_FORMAT_DATE => $history->getCreatedAt()
            ]);
        }

        return $currentHistory;
    }

    /**
     * @return \SM\Sales\Api\Data\StatusHistoryDataInterface
     */
    private function getHistoryTwo()
    {
        return $this->getHistoryByTwoStatus(
            Statuses::IN_PROCESS_WAITING_FOR_PICKUP,
            Statuses::STATUS_IN_PROCESS
        ) ?? $this->getPlaceHolderStatus(Statuses::STATUS_IN_PROCESS);
    }

    /**
     * @return \SM\Sales\Api\Data\StatusHistoryDataInterface
     */
    private function getHistoryThree()
    {
        if ($history = $this->getHistoryByTwoStatus(
            Statuses::STATUS_FAILED_DELIVERY,
            Statuses::STATUS_IN_DELIVERY
        )) {
            return $history;
        } elseif ($history = $this->getHistoryByTwoStatus(
            Statuses::STATUS_IN_DELIVERY,
            Statuses::PICK_UP_BY_CUSTOMER
        )) {
            return $history;
        } else {
            if ($this->order->getShippingMethod() == self::STORE_PICKUP_SHIPPING_CODE) {
                return $this->getPlaceHolderStatus(Statuses::PICK_UP_BY_CUSTOMER);
            } else {
                return $this->getPlaceHolderStatus(Statuses::STATUS_IN_DELIVERY);
            }
        }
    }

    /**
     * @return \SM\Sales\Api\Data\StatusHistoryDataInterface
     */
    private function getHistoryFour()
    {
        return $this->getHistoryByTwoStatus(
            Statuses::STATUS_COMPLETE,
            Statuses::STATUS_DELIVERED
        ) ?? $this->getPlaceHolderStatus(Statuses::STATUS_DELIVERED);
    }

    /**
     * @param $status1
     * @param $status2
     * @return \SM\Sales\Api\Data\StatusHistoryDataInterface
     */
    private function getHistoryByTwoStatus($status1, $status2)
    {
        $historyDetails = $this->historyDetails[$this->order->getId()];

        foreach ($historyDetails as $history) {
            if ($history->getStatus() == $status1
                || $history->getStatus() == $status2) {
                $currentHistory = $this->statusHistoryDataFactory->create();
                $currentHistory->setData([
                    StatusHistoryDataInterface::STATUS => $history->getStatus(),
                    StatusHistoryDataInterface::LABEL => $history->getLabel(),
                    StatusHistoryDataInterface::CREATED_AT => $this->formatDate($history->getCreatedAt()),
                    StatusHistoryDataInterface::ORDER_UPDATE => $history->getComment(),
                    StatusHistoryDataInterface::ICON => $this->getActiveIcon($history->getStatus()),
                    StatusHistoryDataInterface::ICON_CLASS => $this->getActiveIconClass($history->getStatus()),
                    StatusHistoryDataInterface::IS_ACTIVE => true,
                    StatusHistoryDataInterface::RAW_FORMAT_DATE => $history->getCreatedAt()
                ]);

                return $currentHistory;
            }
        }
        return null;
    }

    /**
     * @param $status
     * @return \SM\Sales\Api\Data\StatusHistoryDataInterface
     */
    private function getPlaceHolderStatus($status)
    {
        $history = $this->statusHistoryDataFactory->create();
        $statuses = $this->getAllStatus();

        return $history->setData(
            [
                StatusHistoryDataInterface::STATUS => $status,
                StatusHistoryDataInterface::LABEL => $statuses[$status]['label'],
                StatusHistoryDataInterface::CREATED_AT => null,
                StatusHistoryDataInterface::ICON => $this->getPlaceHolderIcon($status),
                StatusHistoryDataInterface::ICON_CLASS => self::MAP_STATUS_SVG_CLASS[$status],
                StatusHistoryDataInterface::IS_ACTIVE => false,
            ]
        );
    }

    /**
     * @return array
     */
    public function getAllStatus()
    {
        if (empty($this->statuses)) {
            /** @var StatusCollection $statusCollection */
            $statusCollection = $this->orderStatusCollectionFactory->create();
            $result = [];
            /** @var \Magento\Sales\Model\Order\Status $status */
            foreach ($statusCollection as $status) {
                $result[$status->getStatus()] = ['label' => $status->getLabel()];
            }
            $this->statuses = $result;
        }
        return $this->statuses;
    }

    /**
     * @param $status
     * @return string
     */
    private function getActiveIcon($status)
    {
        return str_replace("Grey", 'Green', $this->getPlaceHolderIcon($status));
    }

    /**
     * @param $status
     * @return string
     */
    private function getActiveIconClass($status)
    {
        return str_replace("Grey", 'Green', self::MAP_STATUS_SVG_CLASS[$status]);
    }

    /**
     * @param $status
     * @return string
     */
    private function getCancelIcon($status)
    {
        return str_replace("Grey", 'Red', $this->getPlaceHolderIcon($status));
    }

    /**
     * @param $status
     * @return string
     */
    private function getCancelIconClass($status)
    {
        return str_replace("Grey", 'Red', self::MAP_STATUS_SVG_CLASS[$status]);
    }

    /**
     * @param $status
     * @return string
     */
    private function getPlaceHolderIcon($status)
    {
        return $this->viewRepository->getUrl(self::ICON_PATH . self::MAP_STATUS_SVG[$status]);
    }

    /**
     * @param $date
     * @return string
     */
    private function formatDate($date)
    {
        return $this->timezone->date(strtotime($date))->format('d M Y, h:i A');
    }

    /**
     * @return int
     */
    public function getCancelType()
    {
        return $this->cancelType;
    }
}
