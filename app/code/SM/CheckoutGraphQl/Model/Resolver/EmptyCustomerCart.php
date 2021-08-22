<?php
namespace SM\CheckoutGraphQl\Model\Resolver;

use Magento\Quote\Api\CartRepositoryInterface;
use SM\CheckoutGraphQl\Model\GetCurrentCustomerCart;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Class EmptyCustomerCart
 * @package SM\CheckoutGraphQl\Model\Resolver
 */
class EmptyCustomerCart implements ResolverInterface
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
     * EmptyCustomerCart constructor.
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

        $customerId = $context->getUserId();
        $storeId = $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();

        $cart = $this->getCurrentCustomerCart->execute($customerId, $cartHash, $cartId);

        try {
            $cart->removeAllItems();
            $this->cartRepository->save($cart);
        } catch (\Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }

        return [
            'status' => true,
            'cart' => [
                'model' => $cart,
            ],
        ];
    }
}
