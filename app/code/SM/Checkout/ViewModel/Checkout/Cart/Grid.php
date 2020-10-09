<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 3/25/20
 * Time: 10:49 AM
 */

namespace SM\Checkout\ViewModel\Checkout\Cart;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Grid extends DataObject implements ArgumentInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;

    /**
     * Grid constructor.
     * @param \Magento\Checkout\Model\Session $session
     * @param array $data
     */
    public function __construct(
        \Magento\Checkout\Model\Session $session,
        array $data = []
    ) {
        parent::__construct($data);
        $this->session = $session;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isSelectedAll()
    {
        $items = [];
        foreach ($this->session->getQuote()->getItemsCollection() as $item) {
            if (!$item->isDeleted() && !$item->getParentItemId() && !$item->getParentItem()) {
                $items[] = $item;
            }
        }
        $visibleItems = count($this->session->getQuote()->getAllVisibleItems());
        $mainquoteItems = count($items);

        return $visibleItems == $mainquoteItems;
    }
}