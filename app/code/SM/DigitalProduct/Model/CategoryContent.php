<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use SM\DigitalProduct\Api\Data\CategoryContentExtensionInterface;
use SM\DigitalProduct\Api\Data\CategoryContentInterface;
use SM\DigitalProduct\Model\ResourceModel\CategoryContent as CategoryContentResource;
use SM\DigitalProduct\Model\ResourceModel\CategoryContent\Collection as CategoryContentCollection;

/**
 * Class CategoryContent
 * @package SM\DigitalProduct\Model\Data
 */
class CategoryContent extends AbstractExtensibleModel implements CategoryContentInterface
{
    protected $_eventPrefix = 'sm_digitalproduct_category_store';

    /**
     * CategoryContent constructor.
     * @param Context $context
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param Registry $registry
     * @param CategoryContentResource $resource
     * @param CategoryContentCollection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Registry $registry,
        CategoryContentResource $resource,
        CategoryContentCollection $resourceCollection,
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
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(CategoryContentExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * @inheritDoc
     */
    public function getCategoryName()
    {
        return $this->getData(self::CATEGORY_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setCategoryName($categoryName)
    {
        return $this->setData(self::CATEGORY_NAME, $categoryName);
    }

    /**
     * @inheritDoc
     */
    public function getInformation()
    {
        return $this->getData(self::INFORMATION);
    }

    /**
     * @inheritDoc
     */
    public function setInformation($information)
    {
        return $this->setData(self::INFORMATION, $information);
    }

    /**
     * @inheritDoc
     */
    public function getHowToBuy()
    {
        return $this->getData(self::HOW_TO_BUY);
    }

    /**
     * @inheritDoc
     */
    public function setHowToBuy($howToBuy)
    {
        return $this->setData(self::HOW_TO_BUY, $howToBuy);
    }

    /**
     * @inheritDoc
     */
    public function getTooltip()
    {
        return $this->getData(self::TOOLTIP);
    }

    /**
     * @inheritDoc
     */
    public function setTooltip($value)
    {
        return $this->setData(self::TOOLTIP, $value);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType($value)
    {
        return $this->setData(self::TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getInfo()
    {
        return $this->getData(self::INFO);
    }

    /**
     * @inheritDoc
     */
    public function setInfo($value)
    {
        return $this->setData(self::INFO, $value);
    }

    /**
     * @inheritDoc
     */
    public function getProducts()
    {
        return $this->getData(self::PRODUCTS);
    }

    /**
     * @inheritDoc
     */
    public function setProducts($value)
    {
        return $this->setData(self::PRODUCTS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOperatorImage()
    {
        return $this->getData(self::OPERATOR_IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setOperatorImage($value)
    {
        return $this->setData(self::OPERATOR_IMAGE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOperator()
    {
        return $this->getData(self::OPERATOR);
    }

    /**
     * @inheritDoc
     */
    public function setOperator($value)
    {
        return $this->setData(self::OPERATOR, $value);
    }
}
