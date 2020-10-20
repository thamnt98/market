<?php

namespace SM\Checkout\Preference\Quote\Model\Webapi;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class ParamOverriderCartId
 * @package SM\Checkout\Preference\Quote\Model\Webapi
 */
class ParamOverriderCartId extends \Magento\Quote\Model\Webapi\ParamOverriderCartId
{
    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var CartManagementInterface
     */
    private $cartManagement;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * ParamOverriderCartId constructor.
     * @param UserContextInterface $userContext
     * @param CartManagementInterface $cartManagement
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        UserContextInterface $userContext,
        CartManagementInterface $cartManagement,
        CartRepositoryInterface $cartRepository
    ) {
        parent::__construct(
            $userContext,
            $cartManagement
        );
        $this->userContext = $userContext;
        $this->cartManagement = $cartManagement;
        $this->cartRepository = $cartRepository;
    }
    /**
     * {@inheritDoc}
     */
    public function getOverriddenValue()
    {
        try {
            if ($this->userContext->getUserType() === UserContextInterface::USER_TYPE_CUSTOMER) {
                $customerId = $this->userContext->getUserId();

                /** @var \Magento\Quote\Api\Data\CartInterface */
                $cart = $this->cartRepository->getActiveForCustomer($customerId);
                if ($cart) {
                    return $cart->getId();
                }
            }
        } catch (NoSuchEntityException $e) {
            return $this->cartManagement->createEmptyCartForCustomer($customerId);
//            throw new NoSuchEntityException(__('Current customer does not have an active cart.'));
        }
        return null;
    }
}
