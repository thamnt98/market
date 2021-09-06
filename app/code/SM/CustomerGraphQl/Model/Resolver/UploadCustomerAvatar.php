<?php

namespace SM\CustomerGraphQl\Model\Resolver;

use Magento\Framework\Api\Data\ImageContentInterfaceFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;
use SM\Customer\Model\TransCustomerProfile;
use Trans\CustomerMyProfile\Block\MyProfile\PersonalInformation;

/**
 * Class UploadCustomerAvatar
 * @package SM\CustomerGraphQl\Model\Resolver
 */
class UploadCustomerAvatar extends PersonalInformation implements ResolverInterface
{

    /**
     * @var ImageContentInterfaceFactory
     */
    protected $imageContent;

    /**
     * @var TransCustomerProfile
     */
    protected $customerProfile;

    /**
     * UploadCustomerAvatar constructor.
     * @param ImageContentInterfaceFactory $imageContent
     * @param TransCustomerProfile $customerProfile
     */
    public function __construct(ImageContentInterfaceFactory $imageContent, TransCustomerProfile $customerProfile)
    {
        $this->imageContent = $imageContent;
        $this->customerProfile = $customerProfile;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return bool
     * @throws GraphQlAuthorizationException
     * @throws GraphQlInputException
     * @throws \Magento\Setup\Exception
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        /** @var ContextInterface $context */
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }

        if (empty($args['imageContent']) || !is_array($args['imageContent'])) {
            throw new GraphQlInputException(__('"Image content" value should be specified'));
        }
        $imageContent = $this->imageContent->create();
        $imageContent->setBase64EncodedData($args['imageContent']['base64_encoded_data']);
        $imageContent->setName($args['imageContent']['name']);
        $imageContent->setType($args['imageContent']['type']);
        $customerId = $context->getUserId();
        return $this->customerProfile->uploadCustomerAvatar($customerId, $imageContent);
    }
}
