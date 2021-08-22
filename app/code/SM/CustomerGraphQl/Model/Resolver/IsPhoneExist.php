<?php
namespace SM\CustomerGraphQl\Model\Resolver;

use SM\Customer\Api\TransCustomerRepositoryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Class IsPhoneExist
 * @package SM\CustomerGraphQl\Model\Resolver
 */
class IsPhoneExist implements ResolverInterface
{
    /**
     * @var TransCustomerRepositoryInterface
     */
    protected $transCustomerRepository;

    /**
     * IsPhoneExist constructor.
     * @param TransCustomerRepositoryInterface $transCustomerRepository
     */
    public function __construct(
        TransCustomerRepositoryInterface $transCustomerRepository
    ) {
        $this->transCustomerRepository = $transCustomerRepository;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (empty($args['telephone'])) {
            throw new GraphQlInputException(__('"telephone" value should be specified'));
        }

        $telephone = $args['telephone'];
        $uniquePhone = $this->transCustomerRepository->uniquePhone($telephone);
        return !$uniquePhone;
    }
}
