<?php
/**
 * Class Search
 * @package SM\Checkout\Plugin\Quote
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Checkout\Plugin\Quote\Model;

class Quote
{
    /**
     * @var bool
     */
    protected $isVirtual = true;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * @var \SM\Checkout\Helper\DigitalProduct
     */
    protected $digitalHelper;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * Quote constructor.
     * @param \SM\Checkout\Helper\DigitalProduct $digitalHelper
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \SM\Checkout\Helper\DigitalProduct $digitalHelper
    ) {
        $this->request = $request;
        $this->digitalHelper = $digitalHelper;
    }

    /**
     * Add condition getIsActive from item with select/unselect item on cart
     * Retrieve quote items array
     *
     * @param \Magento\Quote\Model\Quote $subject
     * @param callable $proceed
     * @return array
     */
    public function aroundGetAllItems(
        \Magento\Quote\Model\Quote $subject,
        callable $proceed
    ) {
        $items = [];
        $this->quote = $subject;
        foreach ($subject->getItemsCollection() as $item) {
            if (!$item->getId() || $item->getIsActive() === null) {
                $item->setIsActive(1);
            }
            if ($this->itemIsActive($item)) {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * Add condition getIsActive from item with select/unselect item on cart
     * Get array of all items what can be display directly
     *
     * @param \Magento\Quote\Model\Quote $subject
     * @param callable $proceed
     * @return \Magento\Quote\Model\Quote\Item[]
     */
    public function aroundGetAllVisibleItems(
        \Magento\Quote\Model\Quote $subject,
        callable $proceed
    ) {
        $items = [];
        $this->quote = $subject;
        foreach ($subject->getItemsCollection() as $item) {
            if (!$item->getId() || $item->getIsActive() === null) {
                $item->setIsActive(1);
            }
            if ($this->itemIsActive($item) && !$item->getParentItemId() && !$item->getParentItem()) {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * @param $item
     * @return bool
     */
    protected function itemIsActive($item)
    {
        if (!$item->isDeleted() && $item->getIsActive()) {
            return true;
        } elseif ($this->isCartUpdate() && !$item->isDeleted() && !$item->getIsActive()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    protected function isCartUpdate()
    {
        $currentUrl = $this->request->getFullActionName();
        $listUrl = array('transcheckout_cart_update','checkout_cart_add','checkout_sidebar_UpdateItemQty','checkout_cart_updatePost');
        if (in_array($currentUrl, $listUrl)) {
            return true;
        }
        return false;
    }
}
