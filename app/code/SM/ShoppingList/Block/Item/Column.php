<?php
/**
 * @category Magento
 * @package SM\ShoppingList\Block\Item
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\ShoppingList\Block\Item;

use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Template;
use SM\ShoppingList\Api\Data\ShoppingListItemDataInterface;
use SM\Label\Model\LabelViewer;
use Magento\Framework\Registry;

/**
 * Class Column
 * @package SM\ShoppingList\Block\Item
 */
abstract class Column extends Template
{
    /**
     * @var ShoppingListItemDataInterface
     */
    protected $item;
    /**
     * @var Product
     */
    protected $product;

    /**
     * @var LabelViewer
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Column constructor.
     * @param Template\Context $context
     * @param LabelViewer $helper
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        LabelViewer $helper,
        Registry $registry,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return ShoppingListItemDataInterface
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param ShoppingListItemDataInterface $item
     * @return $this
     */
    public function setItem($item)
    {
        $this->item = $item;
        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @param $product
     * @return $this
     */
    public function setProductLabel($product)
    {
        $this->product = $product;
        return $this;
    }

}
