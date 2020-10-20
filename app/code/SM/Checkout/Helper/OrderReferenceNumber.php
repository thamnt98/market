<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 5/29/20
 * Time: 9:34 AM
 */

namespace SM\Checkout\Helper;

/**
 * Class Order
 * @package SM\Checkout\Helper
 */
class OrderReferenceNumber
{
    const DIGITAL_PRODUCT = 1;
    const PHYSICAL_PRODUCT =2;
    const AREA_WEB = 'web';
    const AREA_APP = 'app';
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $date;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * OrderReferenceNumber constructor.
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->orderFactory = $orderFactory;
        $this->date =  $date;
        $this->checkoutSession =  $checkoutSession;
    }
    /**
     * @return bool
     */
    public function isMobile()
    {
        if ($this->checkoutSession->getArea() == self::AREA_APP) {
            return true;
        }
        return false;
    }

    /**
     * @param $order
     * @return bool
     */
    public function isDigitalProduct($order)
    {
        return ($this->checkOrderProductType($order) == self::DIGITAL_PRODUCT) ? true: false;
    }
    /**
     * @param $order
     * @return bool
     */
    public function isPhysicalProduct($order)
    {
        return ($this->checkOrderProductType($order) == self::PHYSICAL_PRODUCT) ? true: false;
    }

    /**
     * @param $order \Magento\Sales\Model\OrderFactory
     * @return int
     */
    public function checkOrderProductType($order)
    {
        $arr = ['virtual', 'downloadable', 'giftcard'];
        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($order->getAllItems() as $item) {
            if (in_array($item->getProductType(), $arr)) {
                return self::DIGITAL_PRODUCT;
            }
        }
        return self::PHYSICAL_PRODUCT;
    }

    /**
     * @param $order \Magento\Sales\Model\Order
     * @return string
     */
    public function generateReferenceNumber($order)
    {
        $prefix = "";
        if ($this->isDigitalProduct($order)) {
            $prefix = "DP-";
        }
        $device = 'Web-';
        if ($this->isMobile()) {
            $device = 'App-';
        }
        $incrementId = $order->getIncrementId();
        return $prefix.$device.$incrementId;
    }

    /**
     * @param $order \Magento\Sales\Model\OrderFactory
     * @param $mainOrder \Magento\Sales\Model\OrderFactory
     * @param $j
     * @return string
     */
    public function generateSubOrderReferenceNumber($order, $mainOrder, $j = null)
    {
        $referenceNumber = $mainOrder->getReferenceNumber();
        $isPickup = ($order->getShippingMethod() == 'store_pickup_store_pickup')? true: false;
        $shipmentType = ($isPickup)? "-0-": "-1-";
        if ($isPickup) {
            /**
             * one main order only has one suborder pickup
             */
            $number = '01';
        } else {
            $referenceOrderId = explode('-', $order->getData('reference_order_id'));
            if ($order->getIsVirtual()) {
                return $referenceNumber.'-01';
            } elseif (empty($referenceOrderId) || ($order->getId() === $mainOrder->getId())){
                /**
                 * case error when finding fail/null
                 */
                return null;
            } else {

                /**
                 * maximum splitorder are 9 suborder => $number max = 09
                 * so, convert $lastNumber to int and add "0" to prefix
                 */
                $number = '0'. $j;
            }
        }
        return $referenceNumber.$shipmentType.$number;
    }

    /**
     * @param $order
     * @return string
     */
    public function generatePaymentReferenceNumber($order)
    {
        $referenceNumber = $order->getReferenceNumber();
        $referencePaymentNumber = $order->getReferencePaymentNumber();
        if ($referencePaymentNumber == null || $referencePaymentNumber == "") {
            $number = '-01';
        } else {
            $number = '-02';
        }

        return '(Payment) '.$referenceNumber.$number;
    }

    /**
     * @param $order
     * @return string
     */
    public function generateInvoiceReferenceNumber($order)
    {
        $prefix = 'INV.';
        $referenceNumber = str_replace('-', '/', $order->getReferenceNumber());
        $isDigital = $this->isDigitalProduct($order) ? '/DP': '';
        $date = $this->date->date()->format('Y/m/d');

        return $prefix.$date.$isDigital.'/'.$referenceNumber;
    }
}
