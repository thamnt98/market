<?php
namespace SM\CustomerGraphQl\Model\Resolver;

use SM\AndromedaSms\Api\SmsVerificationInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Class SendOtp
 * @package SM\CustomerGraphQl\Model\Resolver
 */
class SendOtp implements ResolverInterface
{
    /**
     * @var SmsVerificationInterface
     */
    protected $smsVerification;

    /**
     * SendOtp constructor.
     * @param SmsVerificationInterface $smsVerification
     */
    public function __construct(
        SmsVerificationInterface $smsVerification
    ) {
        $this->smsVerification = $smsVerification;
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
        $checkExistCustomerPhone = $args['checkExistCustomerPhone'] ?? true;

        return $this->smsVerification->send($telephone, $checkExistCustomerPhone);
    }
}
