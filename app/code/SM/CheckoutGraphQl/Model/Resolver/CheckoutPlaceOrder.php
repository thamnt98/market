<?php
namespace SM\CheckoutGraphQl\Model\Resolver;

use SM\CheckoutGraphQl\Model\GetCurrentCustomerCart;
use SM\Checkout\Api\MultiShippingInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;

/**
 * Class CheckoutPlaceOrder
 * @package SM\CheckoutGraphQl\Model\Resolver
 */
class CheckoutPlaceOrder implements ResolverInterface
{
    /**
     * @var MultiShippingInterface
     */
    protected $multiShipping;

    /**
     * @var GetCurrentCustomerCart
     */
    protected $getCurrentCustomerCart;

    /**
     * CheckoutPlaceOrder constructor.
     * @param MultiShippingInterface $multiShipping
     * @param GetCurrentCustomerCart $getCurrentCustomerCart
     */
    public function __construct(
        MultiShippingInterface $multiShipping,
        GetCurrentCustomerCart $getCurrentCustomerCart
    ) {
        $this->multiShipping = $multiShipping;
        $this->getCurrentCustomerCart = $getCurrentCustomerCart;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $customerId = $context->getUserId();
        $storeId = $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();

        if (!$customerId) {
            throw new GraphQlAuthorizationException(
                __('Guest checkout is not allowed')
            );
        }

        $cart = $this->getCurrentCustomerCart->execute($customerId);

        $placeOrderDataObject = $this->multiShipping->placeOrderMobile($cart->getId());

        return $placeOrderDataObject;
    }
}
