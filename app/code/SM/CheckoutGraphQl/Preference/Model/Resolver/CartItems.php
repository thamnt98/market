<?php
namespace SM\CheckoutGraphQl\Preference\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Model\Quote\Item as QuoteItem;

/**
 * @inheritdoc
 */
class CartItems extends \Magento\QuoteGraphQl\Model\Resolver\CartItems
{
    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }
        $cart = $value['model'];

        $itemsData = [];
        foreach ($cart->getAllVisibleItemsInCart() as $cartItem) {
            /**
             * @var QuoteItem $cartItem
             */
            $productData = $cartItem->getProduct()->getData();
            $productData['model'] = $cartItem->getProduct();

            $itemsData[] = [
                'id' => $cartItem->getItemId(),
                'quantity' => $cartItem->getQty(),
                'product' => $productData,
                'model' => $cartItem,
                'is_active' => $cartItem->getData('is_active')
            ];
        }
        return $itemsData;
    }
}
