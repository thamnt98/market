<?php

namespace SM\Customer\Model\Customer;

use Magento\Framework\Webapi\Exception as HTTPExceptionCodes;

/**
 * Class ResetPasswordToken
 * @package SM\Customer\Model\Customer
 */
class ResetPasswordToken
{
    const EXPIRES_MINUTES = 15;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Oauth\Helper\Oauth
     */
    protected $oauthHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * ResetPasswordToken constructor.
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\Oauth\Helper\Oauth $oauthHelper
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Oauth\Helper\Oauth $oauthHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        $this->resource    = $resource;
        $this->oauthHelper = $oauthHelper;
        $this->timezone    = $timezone;
    }

    /**
     * @param $customerToken
     * @return bool
     * @throws \Exception
     */
    public function addResetPasswordToken($customerToken)
    {
        $connection       = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $tokenTable       = $connection->getTableName('oauth_token');
        $expireResetToken = date("Y-m-d H:i:s", strtotime(sprintf("+%d minutes", self::EXPIRES_MINUTES)));

        try {
            $where = ['token =?' => $customerToken];
            $connection->beginTransaction();
            $connection->update($tokenTable, [
                'reset_password_token' => $this->oauthHelper->generateToken(),
                'expires_reset_token'  => $expireResetToken
            ], $where);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            return false;
        }

        return true;
    }

    /**
     * @param $customerToken
     * @param $customerId
     * @return string
     */
    public function getResetPasswordToken($customerToken, $customerId)
    {
        $connection = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $tokenTable = $connection->getTableName('oauth_token');

        $select = $connection->select();
        $select->from($tokenTable, 'reset_password_token')
            ->where('token = :token AND customer_id = :customer_id');

        return $connection->fetchOne($select, ['token' => $customerToken, 'customer_id' => $customerId]);
    }

    /**
     * @param $resetPasswordToken
     * @param $customerId
     * @return string
     */
    public function getExpiresResetToken($resetPasswordToken, $customerId)
    {
        $connection = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $tokenTable = $connection->getTableName('oauth_token');

        $select = $connection->select();
        $select->from(
            $tokenTable,
            'expires_reset_token'
        )->where('reset_password_token = :reset_password_token AND customer_id = :customer_id');

        return $connection->fetchOne(
            $select,
            ['reset_password_token' => $resetPasswordToken, 'customer_id' => $customerId]
        );
    }

    /**
     * @param $resetPasswordToken
     * @param $customerId
     * @return bool
     * @throws HTTPExceptionCodes
     * @throws \Exception
     */
    public function verifyResetPasswordToken($resetPasswordToken, $customerId)
    {
        $connection = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $tokenTable = $connection->getTableName('oauth_token');

        //Get Current reset_password_token
        $select = $connection->select();
        $select->from(
            $tokenTable,
            'reset_password_token'
        )->where('reset_password_token = :reset_password_token AND customer_id = :customer_id');
        $result = $connection->fetchOne(
            $select,
            ['reset_password_token' => $resetPasswordToken, 'customer_id' => $customerId]
        );

        if (!$result) {
            throw new HTTPExceptionCodes(
                __('Something went wrong, pls try again later!.'),
                0,
                HTTPExceptionCodes::HTTP_FORBIDDEN
            );
        }

        //check expires reset token
        $expiresResetToken          = $this->getExpiresResetToken($resetPasswordToken, $customerId);
        $expiresResetToken          = $this->timezone->date(new \DateTime($expiresResetToken))->format('Y-m-d H:i:s');
        $currentTime                = $this->timezone->date()->format('Y-m-d H:i:s');

        if ($currentTime >= $expiresResetToken) {
            throw new HTTPExceptionCodes(__('Invalid expire time in reset password!.'), 0, HTTPExceptionCodes::HTTP_FORBIDDEN);
        }
        return true;
    }

    /**
     * @param int $customerId
     * @param string $token
     */
    public function removeResetPasswordToken($customerId, $token)
    {
        $connection = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $tokenTable = $connection->getTableName('oauth_token');
        $where      = ['reset_password_token =?' => $token, 'customer_id =?' => $customerId];

        try {
            $connection->beginTransaction();
            $connection->update($tokenTable, [
                'reset_password_token' => null,
                'expires_reset_token'  => null
            ], $where);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }
}
