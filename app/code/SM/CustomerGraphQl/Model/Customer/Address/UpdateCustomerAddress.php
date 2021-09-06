<?php

namespace SM\CustomerGraphQl\Model\Customer\Address;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\CustomerGraphQl\Model\Customer\Address\GetAllowedAddressAttributes;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Customer\Model\ResourceModel\AddressRepository;

/**
 * Class UpdateCustomerAddress
 * @package SM\CustomerGraphQl\Model\Customer\Address
 */
class UpdateCustomerAddress
{
    /**
     * @var GetAllowedAddressAttributes
     */
    private $getAllowedAddressAttributes;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var array
     */
    private $restrictedKeys;

    /**
     * @param GetAllowedAddressAttributes $getAllowedAddressAttributes
     * @param AddressRepository $addressRepository
     * @param DataObjectHelper $dataObjectHelper
     * @param array $restrictedKeys
     */
    public function __construct(
        GetAllowedAddressAttributes $getAllowedAddressAttributes,
        AddressRepository $addressRepository,
        DataObjectHelper $dataObjectHelper,
        array $restrictedKeys = []
    )
    {
        $this->getAllowedAddressAttributes = $getAllowedAddressAttributes;
        $this->addressRepository = $addressRepository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->restrictedKeys = $restrictedKeys;
    }

    /**
     * Update customer address
     *
     * @param AddressInterface $address
     * @param array $data
     * @return void
     * @throws GraphQlInputException
     */
    public function execute(AddressInterface $address, array $data): void
    {
        if (isset($data['country_code'])) {
            $data['country_id'] = $data['country_code'];
        }
        $this->validateData($data);

        if (isset($data['recipient_name'])) {
            // Split Recipient's name to firstname and lastname
            $recipientName = explode(" ", $data['recipient_name']);
            if (count($recipientName) == 1) {
                $data['firstname'] = $data['lastname'] = $recipientName[0];
            } else {
                foreach ($recipientName as $key => $text) {
                    if ($key == 0) {
                        $data['firstname'] = $text;
                    } else {
                        if (isset($data['lastname'])) {
                            $data['lastname'] .= ' ' . $text;
                        } else {
                            $data['lastname'] = $text;
                        }
                    }
                }
            }
            unset($data['recipient_name']);
        }

        $filteredData = array_diff_key($data, array_flip($this->restrictedKeys));
        $this->dataObjectHelper->populateWithArray($address, $filteredData, AddressInterface::class);

        if (isset($data['region']['region_id'])) {
            $address->setRegionId($address->getRegion()->getRegionId());
        }

        try {
            $this->addressRepository->save($address);
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()), $e);
        }
    }

    /**
     * Validate customer address update data
     *
     * @param array $addressData
     * @return void
     * @throws GraphQlInputException
     */
    public function validateData(array $addressData): void
    {
        $attributes = $this->getAllowedAddressAttributes->execute();
        $errorInput = [];

        foreach ($attributes as $attributeName => $attributeInfo) {
            if ($attributeInfo->getIsRequired()
                && (isset($addressData[$attributeName]) && empty($addressData[$attributeName]))
            ) {
                $errorInput[] = $attributeName;
            }
        }

        if ($errorInput) {
            throw new GraphQlInputException(
                __('Required parameters are missing: %1', [implode(', ', $errorInput)])
            );
        }
    }

}
