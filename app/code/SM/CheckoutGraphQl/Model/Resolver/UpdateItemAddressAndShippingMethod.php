<?php
namespace SM\CheckoutGraphQl\Model\Resolver;

use SM\CheckoutGraphQl\Model\GetCurrentCustomerCart;
use Magento\Customer\Api\CustomerRepositoryInterface;
use SM\Checkout\Api\MultiShippingMobileInterface;
use SM\Checkout\Model\MultiShippingHandle;
use SM\Checkout\Helper\Config as CheckoutHelperConfig;
use SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfo\StorePickUpInterfaceFactory;
use SM\Checkout\Api\Data\Checkout\Estimate\AdditionalInfoInterfaceFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;

/**
 * Class UpdateItemAddressAndShippingMethod
 * @package SM\CheckoutGraphQl\Model\Resolver
 */
class UpdateItemAddressAndShippingMethod implements ResolverInterface
{
    /**
     * @var GetCurrentCustomerCart
     */
    protected $getCurrentCustomerCart;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var MultiShippingMobileInterface
     */
    protected $multiShippingMobile;

    /**
     * @var MultiShippingHandle
     */
    protected $multiShippingHandle;

    /**
     * @var CheckoutHelperConfig
     */
    protected $checkoutHelperConfig;

    /**
     * @var StorePickUpInterfaceFactory
     */
    protected $storePickUpInterfaceFactory;

    /**
     * @var AdditionalInfoInterfaceFactory
     */
    protected $additionalInfoInterfaceFactory;

    /**
     * UpdateItemAddressAndShippingMethod constructor.
     * @param GetCurrentCustomerCart $getCurrentCustomerCart
     * @param CustomerRepositoryInterface $customerRepository
     * @param MultiShippingMobileInterface $multiShippingMobile
     * @param MultiShippingHandle $multiShippingHandle
     * @param CheckoutHelperConfig $checkoutHelperConfig
     * @param StorePickUpInterfaceFactory $storePickUpInterfaceFactory
     * @param AdditionalInfoInterfaceFactory $additionalInfoInterfaceFactory
     */
    public function __construct(
        GetCurrentCustomerCart $getCurrentCustomerCart,
        CustomerRepositoryInterface $customerRepository,
        MultiShippingMobileInterface $multiShippingMobile,
        MultiShippingHandle $multiShippingHandle,
        CheckoutHelperConfig $checkoutHelperConfig,
        StorePickUpInterfaceFactory $storePickUpInterfaceFactory,
        AdditionalInfoInterfaceFactory $additionalInfoInterfaceFactory
    ) {
        $this->getCurrentCustomerCart = $getCurrentCustomerCart;
        $this->customerRepository = $customerRepository;
        $this->multiShippingMobile = $multiShippingMobile;
        $this->multiShippingHandle = $multiShippingHandle;
        $this->checkoutHelperConfig = $checkoutHelperConfig;
        $this->storePickUpInterfaceFactory = $storePickUpInterfaceFactory;
        $this->additionalInfoInterfaceFactory = $additionalInfoInterfaceFactory;
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

        if (empty($args['items'])) {
            throw new GraphQlInputException(__('Required parameter "items" is missing'));
        }
        $updatedItems = $args['items'];
        if (!is_array($updatedItems)) {
            throw new GraphQlInputException(__('Parameter "items" need to be an array'));
        }

        $cart = $this->getCurrentCustomerCart->execute($customerId);

        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(
                __('Current customer does\'t exist.')
            );
        }

        $allVisibleItems = $cart->getAllVisibleItems();
        if (empty($allVisibleItems)) {
            throw new GraphQlNoSuchEntityException(__('There are no product to checkout. Please add more item!'));
        }

        $defaultShippingAddressId = $customer->getDefaultShipping() ?: 0;
        $defaultShippingMethod = MultiShippingHandle::DEFAULT_METHOD;
        $weightUnit = $this->checkoutHelperConfig->getWeightUnit();
        $currencySymbol = $this->checkoutHelperConfig->getCurrencySymbol();

        // Default blank additional info
        $storePickUp = $this->storePickUpInterfaceFactory->create();
        $additionalInfo = $this->additionalInfoInterfaceFactory->create()->setStorePickUp($storePickUp);

        $resultItems = [];
        $chosenAddressIds = [];
        foreach ($allVisibleItems as $quoteItem) {
            $shippingAddressId = $defaultShippingAddressId;
            $shippingMethod = $defaultShippingMethod;

            foreach ($updatedItems as $updatedItem) {
                if (isset($updatedItem['item_id']) && $updatedItem['item_id'] == $quoteItem->getId()) {
                    if (isset($updatedItem['address_id'])) {
                        $shippingAddressId = $updatedItem['address_id'];
                    }
                    if (isset($updatedItem['shipping_method'])) {
                        $shippingMethod = $updatedItem['shipping_method'];
                    }
                }
            }

            $chosenAddressIds[$shippingAddressId] = $shippingAddressId;

            $resultItems[] = $this->multiShippingHandle->buildQuoteItemForMobile(
                [],
                $quoteItem,
                $shippingMethod,
                [],
                $weightUnit,
                $currencySymbol,
                $storeId,
                $shippingAddressId,
                $cart->getApplyVoucher(),
                true
            );
        }

        $chosenAddress = [];
        if (!empty($chosenAddressIds)) {
            foreach ($chosenAddressIds as $addressId) {
                $address = new \Magento\Framework\DataObject;
                $address->setId($addressId);
                $chosenAddress[] = $address;
            }
        }

        $checkoutDataObject = $this->multiShippingMobile->saveShippingItems($chosenAddress, $resultItems, $additionalInfo, $customerId, $cart->getId());

        return $checkoutDataObject->__toArray();
    }
}
