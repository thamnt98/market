<?php

namespace SM\Customer\Api;

/**
 * TranSmart Customer CRUD interface.
 * @api
 * @since 100.0.2
 */
interface TransCustomerRepositoryInterface
{
    /**
     * Retrieve customer by Telephone.
     *
     * @param string $telephone
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer with the specified telephone does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByPhone($telephone);

    /**
     * Trans Create or update a customer.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param string $passwordHash
     * @param string $password
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\InputException If bad input is provided
     * @throws \Magento\Framework\Exception\State\InputMismatchException If the provided email is already used
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function transSave(\Magento\Customer\Api\Data\CustomerInterface $customer, $passwordHash = null, $password = null);

    /**
     * @param string $email
     * @return bool
     */
    public function uniqueEmail($email);

    /**
     * @param string $telephone
     * @return bool
     */
    public function uniquePhone($telephone);

    /**
     * @param string $email
     * @return \SM\Customer\Api\Data\ResultInterface | bool
     */
    public function verifyEmail($email);
}
