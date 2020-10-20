<?php
/**
 * @category Magento
 * @package SM\Sales\Block\Order\Button
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Block\Order\Button;
use Magento\Framework\View\Element\Template;
use SM\Sales\Api\Data\DetailItemDataInterface;


class ReorderItem extends Template
{
    /**
     * @var DetailItemDataInterface
     */
    protected $item;

    /**
     * @return DetailItemDataInterface
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param DetailItemDataInterface $item
     * @return $this
     */
    public function setItem($item)
    {
        $this->item = $item;
        return $this;
    }

    /**
     * @return string
     */
    public function getReorderUrl()
    {
        return $this->getUrl("*/order/submitreorder");
    }
}
