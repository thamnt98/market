<?php

namespace SM\Sales\Block\Order\Physical;

use Exception;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template;
use SM\Sales\Api\Data\ParentOrderDataInterface;
use SM\Sales\Block\AbstractDetail;
use SM\Sales\Model\InvoiceRepository;
use SM\Sales\Model\OrderItemRepository;
use SM\Sales\Model\ParentOrderRepository;

/**
 * Class Physical
 * @package SM\Sales\Block\Order
 */
class Detail extends AbstractDetail
{
    /**
     * @return Template|void
     */
    protected function _prepareLayout()
    {
        $orderId = $this->getRequest()->getParam("id", 0);
        if ($orderId) {
            try {
                $this->setParentOrder($this->parentOrderRepository->getById($orderId));
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

    public function pickUpTimeDateFormat($date)
    {
        return $this->timezone->date($date)->format('d F Y');
    }
}
