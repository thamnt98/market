<?php
/**
 * @category Magento
 * @package SM\Sales\Block\Order\Item
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Sales\Block\Order\Item;

use Magento\Framework\View\Element\Template;
use SM\Sales\Api\Data\ItemOptionDataInterface;

/**
 * Class Options
 * @package SM\Sales\Block\Order\Item
 */
class Options extends Template
{
    /**
     * @var ItemOptionDataInterface[]
     */
    protected $options;

    /**
     * @return ItemOptionDataInterface[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param ItemOptionDataInterface[] $options
     * @return Options
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }
}
