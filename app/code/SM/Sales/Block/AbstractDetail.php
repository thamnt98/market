<?php


namespace SM\Sales\Block;

use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template;
use SM\Sales\Api\Data\ParentOrderDataInterface;
use SM\Sales\Model\DigitalOrderRepository;
use SM\Sales\Model\ParentOrderRepository;
use SM\Sales\Model\SubOrderRepository;

/**
 * Class AbstractDetail
 * @package SM\Sales\Block
 */
abstract class AbstractDetail extends Template
{
    private $parentOrder;
    /**
     * @var ParentOrderRepository
     */
    protected $parentOrderRepository;
    /**
     * @var DigitalOrderRepository
     */
    protected $digitalOrderRepository;
    /**
     * @var SubOrderRepository
     */
    protected $subOrderRepository;
    /**
     * @var Data
     */
    protected $priceHelper;
    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var DateTime
     */
    protected $datetime;

    /**
     * Physical constructor.
     * @param ParentOrderRepository $parentOrderRepository
     * @param DigitalOrderRepository $digitalOrderRepository
     * @param SubOrderRepository $subOrderRepository
     * @param Template\Context $context
     * @param Data $priceHelper
     * @param TimezoneInterface $timezone
     * @param DateTime $dateTime
     * @param array $data
     */
    public function __construct(
        ParentOrderRepository $parentOrderRepository,
        DigitalOrderRepository $digitalOrderRepository,
        SubOrderRepository $subOrderRepository,
        Template\Context $context,
        Data $priceHelper,
        TimezoneInterface $timezone,
        DateTime $dateTime,
        array $data = []
    ) {
        $this->datetime = $dateTime;
        $this->subOrderRepository = $subOrderRepository;
        $this->digitalOrderRepository = $digitalOrderRepository;
        $this->timezone = $timezone;
        $this->priceHelper = $priceHelper;
        $this->parentOrderRepository = $parentOrderRepository;
        parent::__construct($context, $data);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setParentOrder($value)
    {
        $this->parentOrder = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParentOrder()
    {
        return $this->parentOrder;
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
        return $this->timezone->date(strtotime($time))->format('d M Y | h:i A');
    }

    /**
     * @return array
     */
    public function getStatuses()
    {
        $result = json_decode($this->subOrderRepository->getStatusLabel(), true);
        return is_array($result) ? $result : [];
    }

    /**
     * @return bool
     */
    public function isMyOrder()
    {
        $controller = $this->getRequest()->getControllerName();
        $route = $this->getRequest()->getRouteName();
        if ($route == "sales" && $controller == "order") {
            return true;
        }
        return false;
    }
}
