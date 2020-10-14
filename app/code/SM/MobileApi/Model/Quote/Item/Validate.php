<?php


namespace SM\MobileApi\Model\Quote\Item;

use Magento\Framework\Webapi\Exception;

/**
 * Class Validate
 * @package SM\MobileApi\Model\Quote\Item
 */
class Validate
{
    const MAXIMUM_QTY_WHOLE_CART = 99;

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param $currentItemQty
     * @param $newQty
     * @throws Exception
     */
    public static function validateQtyInCart($quote, $currentItemQty, $newQty)
    {
        $itemQtyCart    = (int)$quote->getItemsQty();
        $totalQty       = $itemQtyCart + ($newQty - $currentItemQty);

        if ($totalQty > self::MAXIMUM_QTY_WHOLE_CART) {
            throw new Exception(
                __(sprintf('The maximum number of the whole cart does not exceed %s', self::MAXIMUM_QTY_WHOLE_CART)),
                Exception::HTTP_BAD_REQUEST,
                Exception::HTTP_BAD_REQUEST
            );
        }
    }

}
