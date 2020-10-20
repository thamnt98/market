<?php

namespace SM\Sales\Block\Customer\Order;

use SM\Sales\Api\Data\StatusHistoryDataInterface;
use SM\Sales\Api\ParentOrderRepositoryInterface as Statuses;
use SM\Sales\Block\Order\Physical\Detail;

/**
 * Class ProgressBar
 * @package SM\Sales\Block\Customer\Order
 */
class ProgressBar extends Detail
{
    const ACTIVE = "active";

    const SVG_PENDING_PAYMENT = "svg-icon-WaitingForPaymentGrey";
    const SVG_ORDER_CANCELED = "svg-icon-WaitingForPaymentRed";
    const SVG_IN_PROGRESS = "svg-icon-InProcessGrey";
    const SVG_IN_DELIVERY = "svg-icon-InDeliveryGrey";
    const SVG_COMPLETE = "svg-icon-OrderReceivedGrey";
    const SVG_PICK_UP = "svg-icon-PickUpGrey";
    const SVG_READY_PICK_UP = "svg-icon-ReadyPickUpGrey";

    const MAP_STATUS_SVG = [
        Statuses::STATUS_PENDING_PAYMENT => self::SVG_PENDING_PAYMENT,
        Statuses::STATUS_ORDER_CANCELED => self::SVG_ORDER_CANCELED,
        Statuses::STATUS_IN_DELIVERY => self::SVG_IN_DELIVERY,
        Statuses::STATUS_IN_PROCESS => self::SVG_IN_PROGRESS,
        Statuses::STATUS_DELIVERED => self::SVG_COMPLETE,
        Statuses::STATUS_COMPLETE => self::SVG_COMPLETE,
        Statuses::IN_PROCESS_WAITING_FOR_PICKUP => self::SVG_IN_PROGRESS,
        Statuses::PICK_UP_BY_CUSTOMER => self::SVG_PICK_UP,
    ];

    /**
     * @var string
     */
    private $status;

    /**
     * @var StatusHistoryDataInterface[]
     */
    private $statusHistories;

    /**
     * @var array
     */
    private $statuses;

    /**
     * @var string
     */
    private $deliveryMethodCode;

    /**
     * @var bool
     */
    private $isDetail;

    /**
     * @return bool
     */
    public function getIsDetail()
    {
        return $this->isDetail == true;
    }

    /**
     * @param bool $isDetail
     * @return ProgressBar
     */
    public function setIsDetail($isDetail)
    {
        $this->isDetail = $isDetail;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryMethodCode()
    {
        return $this->deliveryMethodCode;
    }

    /**
     * @param string $deliveryMethodCode
     * @return $this
     */
    public function setDeliveryMethodCode($deliveryMethodCode)
    {
        $this->deliveryMethodCode = $deliveryMethodCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setStatus($value)
    {
        $this->status = $value;
        return $this;
    }

    /**
     * @return StatusHistoryDataInterface[]
     */
    public function getStatusHistories()
    {
        return $this->statusHistories;
    }

    /**
     * @param StatusHistoryDataInterface[] $statusHistories
     * @return $this
     */
    public function setStatusHistories($statusHistories)
    {
        $this->statusHistories = $statusHistories;
        return $this;
    }

    /**
     * @return array
     */
    public function getStatuses()
    {
        return $this->statuses;
    }

    /**
     * @param array $statuses
     * @return $this
     */
    public function setStatuses($statuses)
    {
        $this->statuses = $statuses;
        return $this;
    }

    /**
     * @return array
     */
    public function processStatusHistory()
    {
        $statuses = [];
        $count = 0;

        foreach ($this->getStatusHistories() as $history) {
            if (!is_null($history)) {
                $count++;
                $statuses[] = [
                    "active" => $history->getIsActive() ? self::ACTIVE : "",
                    "svg" => $history->getIconClass(),
                    "date" => $history->getRawFormatDate(),
                    "label" => __($history->getLabel())
                ];
            }
        }

        return $statuses;
    }
}
