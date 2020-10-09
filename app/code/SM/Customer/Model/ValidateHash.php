<?php


namespace SM\Customer\Model;

use Magento\Customer\Model\Data\CustomerSecure;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\Exception\InputException;

/**
 * Class ValidateHash
 * @package SM\Customer\Model
 */
class ValidateHash
{
    /**
     * @var Encryptor
     */
    protected $encryptor;

    /**
     * ValidateHash constructor.
     * @param Encryptor $encryptor
     */
    public function __construct(Encryptor $encryptor)
    {
        $this->encryptor = $encryptor;
    }

    /**
     * @param CustomerSecure $customerSecure
     * @param string $newPassword
     * @throws InputException
     * @throws \Exception
     */
    public function validate($customerSecure, $newPassword)
    {
        $currentHash = $customerSecure->getPasswordHash();
        if (is_null($currentHash)) {
            $isSame = false;
        } else {
            $isSame = $this->encryptor->validateHash($newPassword, $currentHash);
        }
        if ($isSame) {
            throw new InputException(
                __("You can't use your old password")
            );
        }
    }
}
