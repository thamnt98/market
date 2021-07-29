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
class UpdateCartItemIsActive implements ResolverInterface
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

        if (empty($args['input'])) {
            throw new GraphQlInputException(__('Required parameter "input" is missing'));
        }
        $cartItems = $args['input'];
        if (!is_array($cartItems)) {
            throw new GraphQlInputException(__('Parameter "input" need to be an array'));
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
            foreach ($cartItems as $item) {
                if ($quoteItem = $cart->getItemById($item['item_id'])) {
                    $isActive = (int) $item['is_active'];
                    $quoteItem->setData('is_active', $isActive);
                    foreach ($quoteItem->getChildren() as $child) {
                        $child->setData('is_active', $isActive);
                    }
                }
            }

            $cart->setData('totals_collected_flag', false);
            $this->cartRepository->save($cart);
        } catch (\Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }

        return true;
    }
}
