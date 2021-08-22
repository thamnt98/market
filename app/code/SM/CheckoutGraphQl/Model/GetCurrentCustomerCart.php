<?php
namespace SM\CheckoutGraphQl\Model;

use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;
use SM\Checkout\Preference\Quote\Model\Quote;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GetCurrentCustomerCart
 * @package SM\CheckoutGraphQl\Model
 */
class GetCurrentCustomerCart
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
     * @param int $customerId
     * @param $cartHash
     * @param $cartId
     * @param $storeId
     * @return Quote
     * @throws GraphQlAuthorizationException
     * @throws GraphQlNoSuchEntityException
     */
    public function execute($customerId = 0, $cartHash = null, $cartId = null, $storeId = null)
    {
        $cart = false;

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
        } else if (false === (bool)$cart->getIsActive()) {
            throw new GraphQlNoSuchEntityException(
                __('Current user does not have an active cart.')
            );
        }

        if ($storeId && (int)$cart->getStoreId() !== $storeId) {
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

        return $cart;
    }
}
