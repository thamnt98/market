<?php

namespace SM\Sales\Block\Order\Listing;

use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template;
use SM\Sales\Api\Data\ParentOrderDataInterface;

/**
 * Class ParentOrderItem
 * @package SM\Sales\Block\Order\Listing
 */
class ParentOrderItem extends Template
{
    /**
     * @var Data
     */
    protected $priceHelper;
    /**
     * @var TimezoneInterface
     */
    protected $timezone;
    /**
     * @var ParentOrderDataInterface
     */
    private $parentOrder;

    private $statuses;

    /**
     * ParentOrderItem constructor.
     * @param Template\Context $context
     * @param Data $priceHelper
     * @param TimezoneInterface $timezone
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $priceHelper,
        TimezoneInterface $timezone,
        array $data = []
    ) {
        $this->priceHelper = $priceHelper;
        $this->timezone = $timezone;
        parent::__construct($context, $data);
    }

    /**
     * @return ParentOrderDataInterface
     */
    public function getParentOrder()
    {
        return $this->parentOrder;
    }

    /**
     * @param ParentOrderDataInterface $parentOrder
     * @return $this
     */
    public function setParentOrder($parentOrder)
    {
        $this->parentOrder = $parentOrder;
        return $this;
    }

    /**
     * @param string $time
     * @return string
     */
    public function timeFormat($time)
    {
        return $this->timezone->date($time)->format('d M Y | h:i A');
    }

    /**
     * @param float $value
     * @return float|string
     */
    public function currencyFormat($value)
    {
        return $this->priceHelper->currency($value, true, false);
    }

    /**
     * @param int $id
     * @return string
     */
    public function getDetailPhysicalUrl($id)
    {
        return $this->getUrl("sales/order/physical", ["id" => $id]);
    }

    /**
     * @param int $id
     * @return string
     */
    public function getDetailDigitalUrl($id)
    {
        return $this->getUrl("sales/order/digital", ["id" => $id]);
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
     * @return ParentOrderItem
     */
    public function setStatuses($statuses)
    {
        $this->statuses = $statuses;
        return $this;
    }

    /**
     * @param \SM\Sales\Api\Data\DetailItemDataInterface $item
     *
     * @return string
     */
    public function getInstallationHtml($item)
    {
        $installation = $item->getInstallationService();
        if (empty($installation)) {
            return '';
        }

        try {
            /** @var \SM\Installation\Block\View $block */
            $block = $this->getLayout()->createBlock(
                \SM\Installation\Block\View::class,
                '',
                ['data' => ['item_id' => $item->getItemId(), 'show_note' => 'false']]
            );
            $block->setInstallationData($installation);

            return $block->toHtml();
        } catch (\Exception $e) {
            return '';
        }
    }
}
