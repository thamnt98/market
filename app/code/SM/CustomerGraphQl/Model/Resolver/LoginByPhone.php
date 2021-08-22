<?php
namespace SM\CustomerGraphQl\Model\Resolver;

use SM\AndromedaSms\Model\SmsVerification\Validator;
use SM\Customer\Helper\Config;
use SM\Customer\Helper\Customer as CustomerHelper;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Session\SessionManagerInterface as CoreSession;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthenticationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class LoginByPhone
 * @package SM\CustomerGraphQl\Model\Resolver
 */
class LoginByPhone implements ResolverInterface
{
    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var Config
     */
    protected $customerConfigHelper;

    /**
     * @var CustomerHelper
     */
    protected $customerHelper;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var CoreSession
     */
    protected $coreSession;

    /**
     * LoginByPhone constructor.
     * @param Validator $validator
     * @param Config $customerConfigHelper
     * @param CustomerHelper $customerHelper
     * @param CustomerRepositoryInterface $customerRepository
     * @param CoreSession $coreSession
     */
    public function __construct(
        Validator $validator,
        Config $customerConfigHelper,
        CustomerHelper $customerHelper,
        CustomerRepositoryInterface $customerRepository,
        CoreSession $coreSession
    ) {
        $this->validator = $validator;
        $this->customerConfigHelper = $customerConfigHelper;
        $this->customerHelper = $customerHelper;
        $this->customerRepository = $customerRepository;
        $this->coreSession = $coreSession;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (empty($args['telephone'])) {
            throw new GraphQlInputException(__('Specify the "email" value.'));
        }
        $telephone = $args['telephone'];

        if (empty($args['password'])) {
            throw new GraphQlInputException(__('Specify the "password" value.'));
        }
        $password = $args['password'];

        try {
            $convertPhoneNumber = $this->customerConfigHelper->trimTelephone($telephone);
            $customerId = (int)$this->customerHelper->getByPhone($convertPhoneNumber);
            $customer = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $e) {
            throw new GraphQlInputException(__('The phone number `%s` does not exits', $telephone));
        }

        try {
            $this->validator->validateVerified($telephone);
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }

        $isCustomerLocked     = $this->customerHelper->isCustomerLock($customer->getId());
        $isRequestTokenLocked = $this->customerHelper->isRequestTokenLock($customer);

        if ($isCustomerLocked || $isRequestTokenLocked) {
            throw new GraphQlAuthenticationException(__('Your account is locked!'));
        }

        try {
            $customerToken = $this->customerHelper->getCustomerToken($customer->getEmail(), $password);
        } catch (\Exception $e) {
            throw new GraphQlAuthenticationException(__('Password does not match. Please try again'));
        }

        if ($customerToken) {
            $this->coreSession->setLoginTypeGtm('Phone Number');
        }

        return ['token' => $customerToken];
    }
}
