<?php

namespace SM\Checkout\Block\DigitalProduct;

use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template;
use SM\Sales\Api\Data\DigitalOrderDataInterface;
use SM\Sales\Model\DigitalOrderRepository;

/**
 * Class Confirmed
 * @package SM\Checkout\Block\DigitalProduct
 */
class Confirmed extends Template
{
    private $digitalOrder;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var Data
     */
    protected $priceHelper;

    /**
     * @var DigitalOrderRepository
     */
    protected $digitalOrderRepository;

    /**
     * Confirmed constructor.
     * @param Template\Context $context
     * @param DigitalOrderRepository $digitalOrderRepository
     * @param TimezoneInterface $timezone
     * @param Data $priceHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        DigitalOrderRepository $digitalOrderRepository,
        TimezoneInterface $timezone,
        Data $priceHelper,
        array $data = []
    ) {
        $this->digitalOrderRepository = $digitalOrderRepository;
        $this->priceHelper = $priceHelper;
        $this->timezone = $timezone;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        $orderId = $this->getRequest()->getParam("id");
        $this->digitalOrder = $this->digitalOrderRepository->getById($orderId);
    }

    /**
     * @return DigitalOrderDataInterface
     */
    public function getDigitalOrder()
    {
        return $this->digitalOrder;
    }

    public function dateFormat($time)
    {
        return $this->timezone->date($time)->format('d M Y | H:i');
    }

    public function currencyFormat($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }
}
