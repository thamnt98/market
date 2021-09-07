<?php
namespace SM\CustomerGraphQl\Model\Resolver;

use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\CustomerGraphQl\Model\Customer\ExtractCustomerData;
use SM\AndromedaSms\Model\SmsVerification\Validator;
use Magento\CustomerGraphQl\Model\Customer\CheckCustomerPassword;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\GraphQl\Model\Query\ContextInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class UpdateCustomerPassword
 * @package SM\CustomerGraphQl\Model\Resolver
 */
class UpdateCustomerPassword implements ResolverInterface
{
    /**
     * @var GetCustomer
     */
    protected $getCustomer;

    /**
     * @var ExtractCustomerData
     */
    protected $extractCustomerData;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var CheckCustomerPassword
     */
    protected $checkCustomerPassword;

    /**
     * @var AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * UpdateCustomerPassword constructor.
     * @param GetCustomer $getCustomer
     * @param ExtractCustomerData $extractCustomerData
     * @param Validator $validator
     * @param CheckCustomerPassword $checkCustomerPassword
     * @param AccountManagementInterface $accountManagement
     */
    public function __construct(
        GetCustomer $getCustomer,
        ExtractCustomerData $extractCustomerData,
        Validator $validator,
        CheckCustomerPassword $checkCustomerPassword,
        AccountManagementInterface $accountManagement
    ) {
        $this->getCustomer = $getCustomer;
        $this->extractCustomerData = $extractCustomerData;
        $this->validator = $validator;
        $this->checkCustomerPassword = $checkCustomerPassword;
        $this->accountManagement = $accountManagement;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        /** @var ContextInterface $context */
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }

        $customer = $this->getCustomer->execute($context);
        $telephone = $customer->getCustomAttribute('telephone')->getValue();

        try {
            $this->validator->validateVerified($telephone);
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }

        if (!isset($args['currentPassword']) || '' == trim($args['currentPassword'])) {
            throw new GraphQlInputException(__('Specify the "currentPassword" value.'));
        }

        if (!isset($args['newPassword']) || '' == trim($args['newPassword'])) {
            throw new GraphQlInputException(__('Specify the "newPassword" value.'));
        }

        $customerId = $context->getUserId();
        $this->checkCustomerPassword->execute($args['currentPassword'], $customerId);

        try {
            $this->accountManagement->changePasswordById($customerId, $args['currentPassword'], $args['newPassword']);
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()), $e);
        }

        $data = $this->extractCustomerData->execute($customer);
        return ['customer' => $data];
    }
}
