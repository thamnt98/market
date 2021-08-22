<?php
namespace SM\CheckoutGraphQl\Model\Resolver;

use SM\CheckoutGraphQl\Model\GetCurrentCustomerCart;
use SM\Checkout\Api\CartTotalRepositoryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;

/**
 * Class GetCheckoutTotal
 * @package SM\CheckoutGraphQl\Model\Resolver
 */
class GetCheckoutTotal implements ResolverInterface
{
    /**
     * @var GetCurrentCustomerCart
     */
    protected $getCurrentCustomerCart;

    /**
     * @var CartTotalRepositoryInterface
     */
    protected $cartTotalRepository;

    /**
     * GetCheckoutTotal constructor.
     * @param GetCurrentCustomerCart $getCurrentCustomerCart
     * @param CartTotalRepositoryInterface $cartTotalRepository
     */
    public function __construct(
        GetCurrentCustomerCart $getCurrentCustomerCart,
        CartTotalRepositoryInterface $cartTotalRepository
    ) {
        $this->getCurrentCustomerCart = $getCurrentCustomerCart;
        $this->cartTotalRepository = $cartTotalRepository;
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

        $cartTotalObject = $this->cartTotalRepository->get($cart->getId());

        return $cartTotalObject;
    }
}
