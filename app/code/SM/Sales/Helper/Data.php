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
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * @var \Magento\Sales\Model\Order\Address\Renderer
     */
    protected $addressRenderer;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

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
        Context $context,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->timezone = $timezone;
        parent::__construct($context);
        $this->sourceRepository = $sourceRepository;
        $this->tranConfig = $tranConfig;
        $this->orderRepository = $orderRepository;
        $this->imageHelper = $imageHelper;
        $this->priceHelper = $priceHelper;
        $this->addressRenderer= $addressRenderer;
        $this->productRepository = $productRepository;
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
     * @return string
     */
    public function getFailedDeliveryStatus()
    {
        $result = $this->tranConfig->getFailedDeliveryStatus();
        if (empty($result)) {
            $result = 'failed_delivery';
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getInProcessWaitingForPickUpStatus()
    {
        $result = $this->tranConfig->getInProcessWaitingPickupStatus();
        if (empty($result)) {
            $result = 'in_process_waiting_for_pickup';
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

    /**
     * @param float $value
     * @return float|string
     */
    public function currencyFormat($value)
    {
        return $this->priceHelper->currency($value, true, false);
    }

    /**
     * Get Thumbnail Product of Order
     * @param $item
     * @param $width
     * @param $height
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductImage($productId, $width = null, $height = null)
    {
        /**
         * @var \Magento\Catalog\Model\Product $product
         */
        $product = $this->productRepository->getById($productId);

        return $this->imageHelper->init($product, 'cart_page_product_thumbnail')
            ->setImageFile($product->getImage())
            ->resize($width, $height)
            ->getUrl();
    }

    public function getReorderUrl($order)
    {
        return $order->getStore()->getUrl(
            'sales/order/submitreorderall',
            [
                'parent_order_id' => $order->getId()
            ]
        );
    }

    /**
     * Render shipping address into html.
     *
     * @param $order
     * @return string|null
     */
    public function getFormattedShippingAddress($order)
    {
        return $order->getIsVirtual()
            ? null
            : $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }
}
