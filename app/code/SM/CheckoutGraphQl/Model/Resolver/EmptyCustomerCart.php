<?php
namespace SM\CheckoutGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;
use Magento\Quote\Model\Quote;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class UpdateCartItemIsActive
 * @package SM\CheckoutGraphQl\Model\Resolver
 */
class EmptyCustomerCart implements ResolverInterface
{
    /**
     * @var MaskedQuoteIdToQuoteIdInterface
     */
    protected $maskedQuoteIdToQuoteId;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @param MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId,
        CartRepositoryInterface $cartRepository
    ) {
        $this->maskedQuoteIdToQuoteId = $maskedQuoteIdToQuoteId;
        $this->cartRepository = $cartRepository;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $cartHash = false;
        $cartId = false;
        $cart = false;
        if (!empty($args['cart_hash'])) {
            $cartHash = $args['cart_hash'];
        } elseif (!empty($args['cart_id'])) {
            $cartId = $args['cart_id'];
        }

        $customerId = $context->getUserId();
        $storeId = $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();

        if ($cartHash) {
            try {
                $cartId = $this->maskedQuoteIdToQuoteId->execute($cartHash);
            } catch (NoSuchEntityException $exception) {
                throw new GraphQlNoSuchEntityException(
                    __('Could not find a cart with ID "%masked_cart_id"', ['masked_cart_id' => $cartHash])
                );
            }
        }

        if ($cartId) {
            try {
                /** @var Quote $cart */
                $cart = $this->cartRepository->get($cartId);
            } catch (NoSuchEntityException $e) {
                throw new GraphQlNoSuchEntityException(
                    __('Could not find a cart with ID "%cart_id"', ['cart_id' => $cartId])
                );
            }
        }

        if (!$cart) {
            if (!$customerId) {
                throw new GraphQlNoSuchEntityException(
                    __('No cart found')
                );
            }

            try {
                /** @var Quote $cart */
                $cart = $this->cartRepository->getActiveForCustomer($customerId);
            } catch (NoSuchEntityException $e) {
                throw new GraphQlNoSuchEntityException(
                    __('Current user does not have an active cart.')
                );
            }
        }

        if (false === (bool)$cart->getIsActive()) {
            throw new GraphQlNoSuchEntityException(
                __('Current user does not have an active cart.')
            );
        }

        if ((int)$cart->getStoreId() !== $storeId) {
            throw new GraphQlNoSuchEntityException(
                __('Wrong store code specified for requested cart.')
            );
        }

        $cartCustomerId = (int)$cart->getCustomerId();

        if ($cartCustomerId != $customerId) {
            throw new GraphQlAuthorizationException(
                __('The current user cannot perform operations on requested cart')
            );
        }

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
