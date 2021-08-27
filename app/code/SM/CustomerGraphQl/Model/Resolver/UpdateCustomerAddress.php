<?php

namespace SM\CustomerGraphQl\Model\Resolver;

use Magento\CustomerGraphQl\Model\Customer\Address\ExtractCustomerAddressData;
use Magento\CustomerGraphQl\Model\Customer\Address\GetCustomerAddress;
use SM\CustomerGraphQl\Model\Customer\Address\UpdateCustomerAddress as UpdateCustomerAddressModel;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class UpdateCustomerAddress
 * @package SM\CustomerGraphQl\Model\Resolver
 */
class UpdateCustomerAddress implements ResolverInterface
{
    /**
     * @var GetCustomerAddress
     */
    private $getCustomerAddress;

    /**
     * @var UpdateCustomerAddressModel
     */
    private $updateCustomerAddress;

    /**
     * @var ExtractCustomerAddressData
     */
    private $extractCustomerAddressData;

    /**
     * @param GetCustomerAddress $getCustomerAddress
     * @param UpdateCustomerAddressModel $updateCustomerAddress
     * @param ExtractCustomerAddressData $extractCustomerAddressData
     */
    public function __construct(
        GetCustomerAddress $getCustomerAddress,
        UpdateCustomerAddressModel $updateCustomerAddress,
        ExtractCustomerAddressData $extractCustomerAddressData
    )
    {
        $this->getCustomerAddress = $getCustomerAddress;
        $this->updateCustomerAddress = $updateCustomerAddress;
        $this->extractCustomerAddressData = $extractCustomerAddressData;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|\Magento\Framework\GraphQl\Query\Resolver\Value|mixed
     * @throws GraphQlAuthorizationException
     * @throws GraphQlInputException
     * @throws \Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    )
    {
        /** @var \Magento\GraphQl\Model\Query\ContextInterface $context */
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }

        if (empty($args['id'])) {
            throw new GraphQlInputException(__('Address "id" value must be specified'));
        }

        if (empty($args['input']) || !is_array($args['input'])) {
            throw new GraphQlInputException(__('"input" value must be specified'));
        }

        $address = $this->getCustomerAddress->execute((int)$args['id'], $context->getUserId());
        $this->updateCustomerAddress->execute($address, $args['input']);

        return $this->extractCustomerAddressData->execute($address);
    }
}
