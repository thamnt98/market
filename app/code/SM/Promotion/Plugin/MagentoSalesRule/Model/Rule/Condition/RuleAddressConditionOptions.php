<?php


namespace SM\Promotion\Plugin\MagentoSalesRule\Model\Rule\Condition;


class RuleAddressConditionOptions
{
    /**
     * @var \SM\Customer\Model\ResourceModel\Address\Attribute\Source\District
     */
    private $districtSource;
    /**
     * @var \SM\Customer\Model\ResourceModel\Address\Attribute\Source\City
     */
    private $citySource;

    /**
     * RuleAddressConditionOptions constructor.
     * @param \SM\Customer\Model\ResourceModel\Address\Attribute\Source\District $districtSource
     * @param \SM\Customer\Model\ResourceModel\Address\Attribute\Source\City $citySource
     */
    public function __construct(
        \SM\Customer\Model\ResourceModel\Address\Attribute\Source\District $districtSource,
        \SM\Customer\Model\ResourceModel\Address\Attribute\Source\City $citySource
    ) {
        $this->districtSource = $districtSource;
        $this->citySource = $citySource;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule\Condition\Address $subject
     * @param $result
     * @return mixed
     * @see \Magento\SalesRule\Model\Rule\Condition\Address::loadAttributeOptions
     */
    public function afterLoadAttributeOptions(\Magento\SalesRule\Model\Rule\Condition\Address $subject, $result)
    {
        $options = $subject->getAttributeOption();
        $options['city'] = __('Shipping City');
        $options['district'] = __('Shipping District');
        $subject->setAttributeOption($options);
        return $result;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule\Condition\Address $subject
     * @param $result
     * @return mixed
     * @see \Magento\SalesRule\Model\Rule\Condition\Address::getInputType
     */
    public function afterGetInputType(\Magento\SalesRule\Model\Rule\Condition\Address $subject, $result)
    {
        switch ($subject->getAttribute()) {
            case 'city':
            case 'district':
                return 'select';

        }
        return $result;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule\Condition\Address $subject
     * @param $result
     * @return mixed
     * @see \Magento\SalesRule\Model\Rule\Condition\Address::getValueElementType
     */
    public function afterGetValueElementType(\Magento\SalesRule\Model\Rule\Condition\Address $subject, $result)
    {
        switch ($subject->getAttribute()) {
            case 'city':
            case 'district':
                return 'select';

        }
        return $result;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule\Condition\Address $subject
     * @param $result
     * @return mixed
     * @see \Magento\SalesRule\Model\Rule\Condition\Address::getValueSelectOptions
     */
    public function afterGetValueSelectOptions(
        \Magento\SalesRule\Model\Rule\Condition\Address $subject,
        $result
    ) {
        switch ($subject->getAttribute()) {
            case 'city':
                $result = $this->getCityOption();
                $updateFlag = true;
                break;
            case 'district':
                $result = $this->getDistrictOption();
                $updateFlag = true;
                break;
        }
        if (!empty($updateFlag)) {
            $subject->setData('value_select_options', $result);
        }

        return $result;
    }

    public function getCityOption()
    {
        return $this->citySource->getAllOptions();
    }

    public function getDistrictOption()
    {
        return $this->districtSource->getRuleConditionOptions();
    }
}