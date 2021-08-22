<?php
namespace SM\CheckoutGraphQl\Model\Resolver;

use SM\Checkout\Api\MultiShippingMobileInterface;
use SM\CheckoutGraphQl\Model\GetCurrentCustomerCart;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;

/**
 * Class InitCheckout
 * @package SM\CheckoutGraphQl\Model\Resolver
 */
class InitCheckout implements ResolverInterface
{
    /**
     * @var MultiShippingMobileInterface
     */
    protected $multiShippingMobile;

    /**
     * @var GetCurrentCustomerCart
     */
    protected $getCurrentCustomerCart;

    /**
     * InitCheckout constructor.
     * @param MultiShippingMobileInterface $multiShippingMobile
     * @param GetCurrentCustomerCart $getCurrentCustomerCart
     */
    public function __construct(
        MultiShippingMobileInterface $multiShippingMobile,
        GetCurrentCustomerCart $getCurrentCustomerCart
    ) {
        $this->multiShippingMobile = $multiShippingMobile;
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

        $checkoutDataObject = $this->multiShippingMobile->initCheckout($customerId, $cart->getId());

        return $checkoutDataObject->__toArray();
    }
}
