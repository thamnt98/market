<?php
/**
 * Created by PhpStorm.
 * User: thelightsp
 * Date: 4/7/20
 * Time: 11:32 AM
 */

namespace SM\Checkout\Block\Onepage;

use Magento\Framework\View\Element\Template;

class Shipping extends Template
{
    /**
     * @var \Magento\Customer\Model\AttributeMetadataDataProvider
     */
    protected $attributeMetadataDataProvider;

    /**
     * @var \Magento\Ui\Component\Form\AttributeMapper
     */
    protected $attributeMapper;

    /**
     * @var \Magento\Checkout\Block\Checkout\AttributeMerger
     */
    protected $merger;

    /**
     * @var \Magento\Customer\Model\Options|mixed|null
     */
    protected $options;

    /**
     * @var \SM\Checkout\Helper\DeliveryType
     */
    protected $deliveryType;

    /**
     * Shipping constructor.
     * @param Template\Context $context
     * @param \Magento\Customer\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider
     * @param \Magento\Ui\Component\Form\AttributeMapper $attributeMapper
     * @param \Magento\Checkout\Block\Checkout\AttributeMerger $merger
     * @param \SM\Checkout\Helper\DeliveryType $deliveryType
     * @param \Magento\Customer\Model\Options|null $options
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Customer\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider,
        \Magento\Ui\Component\Form\AttributeMapper $attributeMapper,
        \Magento\Checkout\Block\Checkout\AttributeMerger $merger,
        \Magento\Checkout\Block\Checkout\DirectoryDataProcessor $directoryDataProcessor,
        \SM\Checkout\Helper\DeliveryType $deliveryType,
        \Magento\Customer\Model\Options $options = null,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->attributeMapper = $attributeMapper;
        $this->deliveryType = $deliveryType;
        $this->directoryDataProcessor = $directoryDataProcessor;
        $this->merger = $merger;
        $this->options = $options ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Customer\Model\Options::class);
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout']) ? $data['jsLayout'] : [];
    }

    /**
     * @return array
     */
    public function getDeliveryType()
    {
        return $this->deliveryType->getDeliveryType();
    }

    /**
     * @return bool
     */
    public function canShowBoth()
    {
        return (count($this->deliveryType->getDeliveryType()) == 2)? true : false;
    }

    /**
     * @return string
     */
    public function getJsLayout()
    {
        $jsLayout = $this->jsLayout;
        $attributesToConvert = [
            'prefix' => [$this->options, 'getNamePrefixOptions'],
            'suffix' => [$this->options, 'getNameSuffixOptions'],
        ];
        $elements = $this->getAddressAttributes();
        $elements = $this->convertElementsToSelect($elements, $attributesToConvert);

        /*if (isset($jsLayout['shipping-block']['children']['step-config']['children']['shipping-rates-validation']['children'])) {
            $jsLayout['shipping-block']['children']['step-config']['children']['shipping-rates-validation']['children'] =
                $this->processShippingChildrenComponents(
                    $jsLayout['shipping-block']['children']['step-config']['children']['shipping-rates-validation']['children']
                );
        }*/

        if (isset($jsLayout['components']['shipping-block']['children']['address-form']['children']['shipping-address-fieldset']['children'])) {
            $fields = $jsLayout['components']['shipping-block']['children']['address-form']['children']['shipping-address-fieldset']['children'];
            $jsLayout['components']['shipping-block']['children']['address-form']['children']['shipping-address-fieldset']['children'] = $this->merger->merge(
                $elements,
                'checkoutProvider',
                'shippingAddress',
                $fields
            );
        }
        $jsLayout = $this->directoryDataProcessor->process($jsLayout);
        return \Zend_Json::encode($jsLayout);
    }

    /**
     * Get address attributes.
     *
     * @return array
     */
    protected function getAddressAttributes()
    {
        /** @var \Magento\Eav\Api\Data\AttributeInterface[] $attributes */
        $attributes = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'customer_address',
            'customer_register_address'
        );

        $elements = [];
        foreach ($attributes as $attribute) {
            $code = $attribute->getAttributeCode();
/*            if ($attribute->getIsUserDefined()) {
                continue;
            }*/
            $elements[$code] = $this->attributeMapper->map($attribute);
            if (isset($elements[$code]['label'])) {
                $label = $elements[$code]['label'];
                $elements[$code]['label'] = __($label);
            }
        }
        return $elements;
    }

    /**
     * Convert elements(like prefix and suffix) from inputs to selects when necessary
     *
     * @param array $elements address attributes
     * @param array $attributesToConvert fields and their callbacks
     * @return array
     */
    protected function convertElementsToSelect($elements, $attributesToConvert)
    {
        $codes = array_keys($attributesToConvert);
        foreach (array_keys($elements) as $code) {
            if (!in_array($code, $codes)) {
                continue;
            }
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $options = call_user_func($attributesToConvert[$code]);
            if (!is_array($options)) {
                continue;
            }
            $elements[$code]['dataType'] = 'select';
            $elements[$code]['formElement'] = 'select';

            foreach ($options as $key => $value) {
                $elements[$code]['options'][] = [
                    'value' => $key,
                    'label' => $value,
                ];
            }
        }
        return $elements;
    }
}
