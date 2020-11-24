<?php
/**
 * @category SM
 * @package SM_Theme
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Chinhvd <chinhvd@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);

namespace SM\Sales\Block\Order;

use Magento\Customer\Model\Session;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template;
use SM\Sales\Api\Data\ReorderQuickly\OrderDataInterface;
use SM\Sales\Model\ParentOrderRepository;

/**
 * Class ReorderQuickly
 * @package SM\Theme\Block\Order\Widget
 */
class ReorderQuicklyContent extends Template
{
    /**
     * @var ParentOrderRepository
     */
    protected $parentOrderRepository;
    /**
     * @var Session
     */
    protected $customerSession;
    /**
     * @var Data
     */
    protected $priceHelper;
    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    private $orders;
    /**
     * ReorderQuickly constructor.
     * @param Template\Context $context
     * @param ParentOrderRepository $parentOrderRepository
     * @param Session $customerSession
     * @param Data $priceHelper
     * @param TimezoneInterface $timezone
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ParentOrderRepository $parentOrderRepository,
        Session $customerSession,
        Data $priceHelper,
        TimezoneInterface $timezone,
        array $data = []
    ) {
        $this->timezone = $timezone;
        $this->priceHelper = $priceHelper;
        $this->customerSession = $customerSession;
        $this->parentOrderRepository = $parentOrderRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return OrderDataInterface[]
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param OrderDataInterface[] $value
     * @return $this
     */
    public function setOrders($value)
    {
        $this->orders = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getLinkToCompletedOrder()
    {
        return $this->getUrl("sales/order/history", ["tab" => ParentOrderRepository::COMPLETED]);
    }

    /**
     * @param int $orderId
     * @return string
     */
    public function getLinkToOrderDetail($orderId)
    {
        return $this->getUrl("sales/order/physical", ["id" => $orderId]);
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
     * @param string $time
     * @return string
     */
    public function timeFormat($time)
    {
        return $this->timezone->date(strtotime($time))->format('d F Y');
    }
}
