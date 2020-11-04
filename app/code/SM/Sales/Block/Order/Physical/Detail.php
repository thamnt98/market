<?php

namespace SM\Sales\Block\Order\Physical;

use Exception;
use Magento\Customer\Model\Session;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template;
use SM\Sales\Block\AbstractDetail;
use SM\Sales\Model\DigitalOrderRepository;
use SM\Sales\Model\ParentOrderRepository;
use SM\Sales\Model\SubOrderRepository;

/**
 * Class Physical
 * @package SM\Sales\Block\Order
 */
class Detail extends AbstractDetail
{
    /**
     * @var Session
     */
    protected $session;

    public function __construct(
        ParentOrderRepository $parentOrderRepository,
        DigitalOrderRepository $digitalOrderRepository,
        SubOrderRepository $subOrderRepository,
        Template\Context $context,
        Data $priceHelper,
        TimezoneInterface $timezone,
        DateTime $dateTime,
        Session $customerSession,
        array $data = []
    ) {
        $this->session = $customerSession;
        parent::__construct($parentOrderRepository, $digitalOrderRepository, $subOrderRepository, $context, $priceHelper, $timezone, $dateTime, $data);
    }

    /**
     * @return Template|void
     */
    protected function _prepareLayout()
    {
        $orderId = $this->getRequest()->getParam("id", 0);
        if ($orderId) {
            try {
                $customerId = $this->session->getCustomerId();
                $this->setParentOrder($this->parentOrderRepository->getById($customerId, $orderId));
                return;
            } catch (Exception $e) {
                $this->setParentOrder(0);
            }
        }
        $this->setParentOrder(0);
    }

    /**
     * @return string
     */
    public function getReorderAllUrl()
    {
        return $this->getUrl("sales/order/submitreorderall");
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
                ['data' => ['item_id' => $item->getItemId(), 'show_note' => 'true']]
            );
            $block->setInstallationData($installation);

            return $block->toHtml();
        } catch (\Exception $e) {
            return '';
        }
    }

    public function prepareStreet($street)
    {
        $streetPart = explode(", ", $street);
        $firstLine = $secondLine = "";
        if (count($streetPart)) {
            $secondLine = array_pop($streetPart);
            $firstLine = implode(", ", $streetPart);
        }
        return [
            "street" => $firstLine,
            "last" => $secondLine
        ];
    }

    public function getStoreAddress($storeInfo)
    {
        $storeAddress = $storeInfo->getStreet();
        if ($storeInfo->getCity() != '') {
            $storeAddress .= ', ' . $storeInfo->getCity();
        }
        if ($storeInfo->getRegion() != '') {
            $storeAddress .= ', ' . $storeInfo->getRegion();
            if ($storeInfo->getPostcode() != '') {
                $storeAddress .= ' ' . $storeInfo->getPostcode();
            }
        }
        return $storeAddress;
    }

    public function pickUpTimeDateFormat($date)
    {
        return $this->timezone->date($date)->format('d F Y');
    }
}
