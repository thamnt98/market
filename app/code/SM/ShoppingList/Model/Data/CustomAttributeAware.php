<?php

namespace SM\ShoppingList\Model\Data;

trait CustomAttributeAware
{
    /**
     * @var CustomAttribute[]
     */
    private $customAttributes = [];

    /**
     * @param CustomAttribute[] $customAttributes
     *
     * @return $this
     */
    public function setCustomAttributes(array $customAttributes)
    {
        $this->customAttributes = $customAttributes;

        return $this;
    }

    /**
     * @return CustomAttribute[]
     */
    public function getCustomAttributes()
    {
        return $this->customAttributes;
    }

    /**
     * Get an attribute value.
     *
     * @param string $attributeCode
     *
     * @return \Magento\Framework\Api\AttributeInterface|null
     */
    public function getCustomAttribute($attributeCode)
    {
        foreach ($this->customAttributes as $customAttribute) {
            if ($attributeCode === $customAttribute->getName()) {
                return $customAttribute;
            }
        }
    }

    /**
     * Set an attribute value for a given attribute code.
     *
     * @param string $attributeCode
     * @param mixed  $attributeValue
     *
     * @return $this
     */
    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        foreach ($this->customAttributes as $customAttribute) {
            if ($attributeCode === $customAttribute->getName()) {
                $customAttribute->setValue($attributeValue);

                return $this;
            }
        }

        $customAttribute = new CustomAttribute();
        $customAttribute->setName($attributeCode);
        $customAttribute->setValue($attributeValue);

        $this->customAttributes[] = $customAttribute;

        return $this;
    }
}
