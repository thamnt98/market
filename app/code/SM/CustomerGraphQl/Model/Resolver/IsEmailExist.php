<?php
namespace SM\CustomerGraphQl\Model\Resolver;

use SM\Customer\Api\TransCustomerRepositoryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Class IsEmailExist
 * @package SM\CustomerGraphQl\Model\Resolver
 */
class IsEmailExist implements ResolverInterface
{
    /**
     * @var TransCustomerRepositoryInterface
     */
    protected $transCustomerRepository;

    /**
     * IsEmailExist constructor.
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
        if (empty($args['email'])) {
            throw new GraphQlInputException(__('"email" value should be specified'));
        }

        $email = $args['email'];
        $uniqueEmail = $this->transCustomerRepository->uniqueEmail($email);
        return !$uniqueEmail;
    }
}
