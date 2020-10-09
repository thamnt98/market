<?php

declare(strict_types=1);

namespace SM\Customer\Plugin\Magento\Customer\Model;

use Magento\Customer\Model\AuthenticationInterface as BaseAuthenticationInterface;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\UserLockedException;
use SM\Customer\Model\Customer\Data\Checker as DataChecker;

class AuthenticationInterface
{
    /**
     * @var DataChecker
     */
    protected $dataChecker;

    /**
     * AuthenticationInterface constructor.
     * @param DataChecker $dataChecker
     */
    public function __construct(
        DataChecker $dataChecker
    ) {
        $this->dataChecker = $dataChecker;
    }

    /**
     * @param BaseAuthenticationInterface $subject
     * @param bool $result
     * @param int $customerId
     * @param string $password
     * @return bool
     * @throws LocalizedException
     */
    public function afterAuthenticate(BaseAuthenticationInterface $subject, bool $result, int $customerId, string $password)
    {
//        if ($this->dataChecker->isUsingEmail()) {
//            if (!$this->dataChecker->isEmailVerified($customerId)) {
//                throw new LocalizedException(__('Email is unverified, please check mail to verify account first'));
//            }
//        }
//
//        return $result;
    }

    /**
     * @param BaseAuthenticationInterface $subject
     * @param \Closure $proceed,
     * @param int $customerId
     * @param string $password
     * @return bool
     * @throws UserLockedException
     * @throws InvalidEmailOrPasswordException
     * @throws \Exception
     */
    public function aroundAuthenticate(
        BaseAuthenticationInterface $subject,
        \Closure $proceed,
        int $customerId,
        string $password
    ) {
        try {
            return $proceed($customerId, $password);
        } catch (UserLockedException $exception) {
            throw new UserLockedException(__($exception->getMessage()));
        } catch (InvalidEmailOrPasswordException $exception) {
            if (!$this->dataChecker->isUsingEmail()) {
                throw new LocalizedException(
                    __('Your mobile number and password do not match. Please try again')
                );
            }
            throw new LocalizedException(
                __('Your email and password do not match. Please try again')
            );
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
