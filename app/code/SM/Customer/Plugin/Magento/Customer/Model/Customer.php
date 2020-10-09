<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Customer
 *
 * Date: April, 24 2020
 * Time: 11:29 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Customer\Plugin\Magento\Customer\Model;

class Customer
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    protected $customerOption;
    /**
     * Customer constructor.
     *
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\UrlInterface $url,
        \SM\Customer\Api\Data\CustomerOptionInterfaceFactory $customerOption
    ) {
        $this->eavConfig = $eavConfig;
        $this->url = $url;
        $this->customerOption = $customerOption;
    }

    /**
     * @param \Magento\Customer\Model\Customer             $subject
     * @param \Magento\Customer\Api\Data\CustomerInterface $result
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function afterGetDataModel(
        \Magento\Customer\Model\Customer $subject,
        $result
    ) {
        $avatarAttr = $result->getCustomAttribute('profile_picture');
        if ($avatarAttr && $avatarAttr->getValue()) {
            $ext = $result->getExtensionAttributes();
            $ext->setAvatar(
                $this->url->getUrl(
                    'customermyprofile/myprofile/profilepictureview',
                    ['image' => base64_encode($avatarAttr->getValue())]
                )
            );

            $result->setExtensionAttributes($ext);
        }

        $maritalOption = $this->getMaritalData();
        $genderOption = $this->getGenderData();

        $ext = $result->getExtensionAttributes();
        $ext->setMaritalStatusOption($maritalOption);
        $ext->setGenderOption($genderOption);

        $result->setExtensionAttributes($ext);

        return $result;
    }

    /**
     * @return array
     */
    protected function getMaritalData(){
        $attribute = $this->eavConfig->getAttribute('customer', 'marital_status');
        $options = $attribute->getSource()->getAllOptions();
        $maritalData = [];
        foreach ($options as $option){
            if($option["value"] == "" || $option["value"] == null) continue;
            $maritalObject = $this->customerOption->create();
            $maritalObject->setAttributeCode("marital_status");
            $maritalObject->setOptionLabel($option["label"]);
            $maritalObject->setOptionValue($option["value"]);
            $maritalData[] = $maritalObject;
        }
        return $maritalData;
    }

    /**
     * @return array
     */
    protected function getGenderData(){
        $attribute = $this->eavConfig->getAttribute('customer', 'gender');
        $options = $attribute->getSource()->getAllOptions();
        $genderData = [];
        foreach ($options as $option){
            if($option["value"] == "" || $option["value"] == null) continue;
            $genderObject = $this->customerOption->create();
            $genderObject->setAttributeCode("gender");
            $genderObject->setOptionLabel($option["label"]);
            $genderObject->setOptionValue($option["value"]);
            $genderData[] = $genderObject;
        }
        return $genderData;
    }
}
