<?php
namespace SM\CheckoutGraphQl\Model\Resolver;

use SM\CheckoutGraphQl\Model\GetCurrentCustomerCart;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Class UpdateCartItemIsActive
 * @package SM\CheckoutGraphQl\Model\Resolver
 */
class UpdateCartItemIsActive implements ResolverInterface
{
    /**
     * @var GetCurrentCustomerCart
     */
    protected $getCurrentCustomerCart;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * UpdateCartItemIsActive constructor.
     * @param GetCurrentCustomerCart $getCurrentCustomerCart
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        GetCurrentCustomerCart $getCurrentCustomerCart,
        CartRepositoryInterface $cartRepository
    ) {
        $this->getCurrentCustomerCart = $getCurrentCustomerCart;
        $this->cartRepository = $cartRepository;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $cartHash = false;
        $cartId = false;
        if (!empty($args['cart_hash'])) {
            $cartHash = $args['cart_hash'];
        } elseif (!empty($args['cart_id'])) {
            $cartId = $args['cart_id'];
        }

        if (empty($args['input'])) {
            throw new GraphQlInputException(__('Required parameter "input" is missing'));
        }
        $cartItems = $args['input'];
        if (!is_array($cartItems)) {
            throw new GraphQlInputException(__('Parameter "input" need to be an array'));
        }

        $customerId = $context->getUserId();
        $storeId = $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();

        $cart = $this->getCurrentCustomerCart->execute($customerId, $cartHash, $cartId);

        try {
            foreach ($cartItems as $item) {
                if ($quoteItem = $cart->getItemById($item['item_id'])) {
                    if (isset($item['is_active'])) {
                        $isActive = (int)$item['is_active'];
                        $quoteItem->setData('is_active', $isActive);
                        foreach ($quoteItem->getChildren() as $child) {
                            $child->setData('is_active', $isActive);
                        }
                    }

                    if (isset($item['qty'])) {
                        $qty = (double)$item['qty'];
                        $quoteItem->setQty($qty);

                        if ($quoteItem->getHasError()) {
                            throw new \Exception(__($quoteItem->getMessage()));
                        }
                    }
                }
            }

            $cart->setData('totals_collected_flag', false);
            $this->cartRepository->save($cart);
        } catch (\Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }

        return [
            'cart' => [
                'model' => $cart,
            ],
        ];
    }
}
