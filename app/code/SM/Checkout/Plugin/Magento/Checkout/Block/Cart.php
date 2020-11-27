<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 3/25/20
 * Time: 9:47 AM
 */

namespace SM\Checkout\Plugin\Magento\Checkout\Block;

class Cart
{
    /**
     * @param \Magento\Checkout\Block\Cart      $subject
     * @param \Magento\Quote\Model\Quote\Item[] $result
     *
     * @return mixed
     */
    public function afterGetItems($subject, $result)
    {
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($result as $key => $item) {
            if ($item->getParentItemId() ||
                $item->isDeleted() ||
                ($subject->getQuote()->getIsVirtual() && !$item->getIsVirtual()) ||
                (!$subject->getQuote()->getIsVirtual() && $item->getIsVirtual())
            ) {
                unset($result[$key]);
            }
        }

        return $result;
    }
}
