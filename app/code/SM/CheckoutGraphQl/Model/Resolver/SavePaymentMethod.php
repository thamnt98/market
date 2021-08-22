<?php
namespace SM\CheckoutGraphQl\Model\Resolver;

use SM\CheckoutGraphQl\Model\GetCurrentCustomerCart;
use SM\Checkout\Api\MultiShippingMobileInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;

/**
 * Class SavePaymentMethod
 * @package SM\CheckoutGraphQl\Model\Resolver
 */
class SavePaymentMethod implements ResolverInterface
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
     * SavePaymentMethod constructor.
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

        if (empty($args['payment_method'])) {
            throw new GraphQlInputException(__('Required parameter "payment_method" is missing'));
        }
        $paymentMethod = $args['payment_method'];

        $cart = $this->getCurrentCustomerCart->execute($customerId);

        $checkoutDataObject = $this->multiShippingMobile->saveMobilePayment($paymentMethod, null, $customerId, $cart->getId());

        return $checkoutDataObject->getCheckoutTotal();
    }
}
