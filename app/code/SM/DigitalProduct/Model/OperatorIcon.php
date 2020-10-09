<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use SM\DigitalProduct\Api\Data\OperatorIconExtensionInterface;
use SM\DigitalProduct\Api\Data\OperatorIconInterface;
use SM\DigitalProduct\Model\ResourceModel\OperatorIcon as OperatorIconResource;
use SM\DigitalProduct\Model\ResourceModel\OperatorIcon\Collection as OperatorIconCollection;

/**
 * Class OperatorIcon
 * @package SM\DigitalProduct\Model\Data
 */
class OperatorIcon extends AbstractExtensibleModel implements OperatorIconInterface
{
    protected $_eventPrefix = 'sm_digitalproduct_operator_icon';

    /**
     * @param Context $context
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param Registry $registry
     * @param OperatorIconResource $resource
     * @param OperatorIconCollection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Registry $registry,
        OperatorIconResource $resource,
        OperatorIconCollection $resourceCollection,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @inheritDoc
     */
    public function getOperatorIconId()
    {
        return $this->getData(self::OPERATOR_ICON_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOperatorIconId($operatorIconId)
    {
        return $this->setData(self::OPERATOR_ICON_ID, $operatorIconId);
    }

    /**
     * @inheritDoc
     */
    public function getBrandId()
    {
        return $this->getData(self::BRAND_ID);
    }

    /**
     * @inheritDoc
     */
    public function setBrandId($brandId)
    {
        return $this->setData(self::BRAND_ID, $brandId);
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(OperatorIconExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return $this->getData(self::ICON);
    }

    /**
     * @inheritDoc
     */
    public function setIcon($icon)
    {
        return $this->setData(self::ICON, $icon);
    }
}
