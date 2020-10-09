<?php
/**
 * Class CustomAttributesProcessor
 * @package SM\Customer\Plugin\Magento\Customer\Model\Address
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */


namespace SM\Customer\Plugin\Magento\Customer\Model\Address;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Eav\Api\AttributeOptionManagementInterface;
use Trans\LocationCoverage\Model\ResourceModel\District\CollectionFactory as DistrictCollectionFactory;

class CustomAttributesProcessor
{
    /**
     * @var AddressMetadataInterface
     */
    private $addressMetadata;

    /**
     * @var AttributeOptionManagementInterface
     */
    private $attributeOptionManager;

    /**
     * @var DistrictFactory
     */
    protected $districtCollectionFactory;

    /**
     * @param AddressMetadataInterface $addressMetadata
     * @param AttributeOptionManagementInterface $attributeOptionManager
     * @param DistrictCollectionFactory $districtCollectionFactory
     */
    public function __construct(
        AddressMetadataInterface $addressMetadata,
        AttributeOptionManagementInterface $attributeOptionManager,
        DistrictCollectionFactory $districtCollectionFactory
    ) {
        $this->addressMetadata = $addressMetadata;
        $this->attributeOptionManager = $attributeOptionManager;
        $this->districtCollectionFactory = $districtCollectionFactory;
    }

    /**
     * Set Labels to custom Attributes
     *
     * @param \Magento\Framework\Api\AttributeValue[] $customAttributes
     * @return array $customAttributes
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    private function setLabelsForAttributes(array $customAttributes): array
    {
        if (!empty($customAttributes)) {
            foreach ($customAttributes as $customAttributeCode => $customAttribute) {
                if (!$customAttributeCode == 'district') {
                    $attributeOptionLabels = $this->getAttributeLabels($customAttribute, $customAttributeCode);
                } else {
                    $attributeOptionLabels = $this->getDistrictAttributeLabel($customAttribute);
                }
                if (!empty($attributeOptionLabels)) {
                    $customAttributes[$customAttributeCode]['label'] = implode(', ', $attributeOptionLabels);
                }
            }
        }

        return $customAttributes;
    }

    /**
     * Get Labels by CustomAttribute and CustomAttributeCode
     *
     * @param array $customAttribute
     * @param string $customAttributeCode
     * @return array $attributeOptionLabels
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    private function getAttributeLabels(array $customAttribute, string $customAttributeCode) : array
    {
        $attributeOptionLabels = [];

        if (!empty($customAttribute['value'])) {
            $customAttributeValues = explode(',', $customAttribute['value']);
            $attributeOptions = $this->attributeOptionManager->getItems(
                \Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY,
                $customAttributeCode
            );

            if (!empty($attributeOptions)) {
                foreach ($attributeOptions as $attributeOption) {
                    $attributeOptionValue = $attributeOption->getValue();
                    if (\in_array($attributeOptionValue, $customAttributeValues, false)) {
                        $attributeOptionLabels[] = $attributeOption->getLabel() ?? $attributeOptionValue;
                    }
                }
            }
        }

        return $attributeOptionLabels;
    }

    /**
     * @param $customAttribute
     * @return array
     */
    private function getDistrictAttributeLabel($customAttribute)
    {
        if (!empty($customAttribute['value'])
            && (is_string($customAttribute['value']) || is_numeric($customAttribute['value']))) {
            $district = $this->districtCollectionFactory->create()
                ->addFieldToFilter('district_id', $customAttribute['value'])
                ->getFirstItem();

            return $district->getId() ? [$district->getDistict()] : [];
        }

        return [];
    }

    /**
     * @param $subject
     * @param callable $callable
     * @param $attributes
     * @return \Magento\Framework\Api\AttributeValue[]
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function aroundFilterNotVisibleAttributes(
        \Magento\Customer\Model\Address\CustomAttributesProcessor $subject,
        callable $callable,
        array $attributes
    ) {
        $attributesMetadata = $this->addressMetadata->getAllAttributesMetadata();
        foreach ($attributesMetadata as $attributeMetadata) {
            if (!$attributeMetadata->isVisible()) {
                unset($attributes[$attributeMetadata->getAttributeCode()]);
            }
        }
        return $this->setLabelsForAttributes($attributes);
    }
}
