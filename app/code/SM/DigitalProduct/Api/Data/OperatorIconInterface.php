<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Api\Data;

interface OperatorIconInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const ICON = 'icon';
    const OPERATOR_ICON_ID = 'operator_icon_id';
    const BRAND_ID = 'operator_service';

    /**
     * Get operator_icon_id
     * @return string|null
     */
    public function getOperatorIconId();

    /**
     * Set operator_icon_id
     * @param string $operatorIconId
     * @return \SM\DigitalProduct\Api\Data\OperatorIconInterface
     */
    public function setOperatorIconId($operatorIconId);

    /**
     * Get operator_service
     * @return string|null
     */
    public function getBrandId();

    /**
     * Set operator_service
     * @param string $brandId
     * @return \SM\DigitalProduct\Api\Data\OperatorIconInterface
     */
    public function setBrandId($brandId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \SM\DigitalProduct\Api\Data\OperatorIconExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \SM\DigitalProduct\Api\Data\OperatorIconExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \SM\DigitalProduct\Api\Data\OperatorIconExtensionInterface $extensionAttributes
    );

    /**
     * Get icon
     * @return string|null
     */
    public function getIcon();

    /**
     * Set icon
     * @param string $icon
     * @return \SM\DigitalProduct\Api\Data\OperatorIconInterface
     */
    public function setIcon($icon);
}

