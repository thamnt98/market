<?php
namespace SM\CustomerGraphQl\Model\Resolver;

use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\CustomerGraphQl\Model\Customer\SaveCustomer;
use Magento\CustomerGraphQl\Model\Customer\ExtractCustomerData;
use SM\AndromedaSms\Model\SmsVerification\Validator;
use SM\Customer\Api\TransCustomerRepositoryInterface;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\GraphQl\Model\Query\ContextInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class UpdateCustomerEmailPhone
 * @package SM\CustomerGraphQl\Model\Resolver
 */
class UpdateCustomerEmailPhone implements ResolverInterface
{
    /**
     * @var GetCustomer
     */
    protected $getCustomer;

    /**
     * @var SaveCustomer
     */
    protected $saveCustomer;

    /**
     * @var ExtractCustomerData
     */
    protected $extractCustomerData;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var TransCustomerRepositoryInterface
     */
    protected $transCustomerRepository;

    /**
     * UpdateCustomerEmailPhone constructor.
     * @param GetCustomer $getCustomer
     * @param SaveCustomer $saveCustomer
     * @param ExtractCustomerData $extractCustomerData
     * @param Validator $validator
     * @param TransCustomerRepositoryInterface $transCustomerRepository
     */
    public function __construct(
        GetCustomer $getCustomer,
        SaveCustomer $saveCustomer,
        ExtractCustomerData $extractCustomerData,
        Validator $validator,
        TransCustomerRepositoryInterface $transCustomerRepository
    ) {
        $this->getCustomer = $getCustomer;
        $this->saveCustomer = $saveCustomer;
        $this->extractCustomerData = $extractCustomerData;
        $this->validator = $validator;
        $this->transCustomerRepository = $transCustomerRepository;
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

        if (isset($args['input']['email'])) {
            $newEmail = $args['input']['email'];
            if (!$this->transCustomerRepository->uniqueEmail($newEmail)) {
                throw new GraphQlInputException(__('This email has already been registered'));
            }
            $customer->setEmail($newEmail);
        }

        if (isset($args['input']['telephone'])) {
            $newPhone = $args['input']['telephone'];
            if (!$this->transCustomerRepository->uniquePhone($newPhone)) {
                throw new GraphQlInputException(__('This phone number has already been registered'));
            }
            $customer->setCustomAttribute('telephone', $newPhone);
        }

        $this->saveCustomer->execute($customer);

        $data = $this->extractCustomerData->execute($customer);
        return ['customer' => $data];
    }
}
