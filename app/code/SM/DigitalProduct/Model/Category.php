<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Api\ExtensionAttributesInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use SM\DigitalProduct\Api\Data\CategoryExtensionInterface;
use SM\DigitalProduct\Api\Data\CategoryInterface;
use SM\DigitalProduct\Model\ResourceModel\Category as CategoryResource;
use SM\DigitalProduct\Model\ResourceModel\Category\Collection as CategoryCollection;

/**
 * Class Category
 * @package SM\DigitalProduct\Model
 */
class Category extends AbstractExtensibleModel implements CategoryInterface
{
    protected $_eventPrefix = 'sm_digitalproduct_category';

    /**
     * Category constructor.
     * @param Context $context
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param CategoryResource $resource
     * @param CategoryCollection $resourceCollection
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        CategoryResource $resource,
        CategoryCollection $resourceCollection,
        Registry $registry,
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
     * @inheritdoc
     *
     * @return ExtensionAttributesInterface
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param CategoryExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        CategoryExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get thumbnail
     * @return string|null
     */
    public function getThumbnail()
    {
        return $this->getData(self::THUMBNAIL);
    }

    /**
     * Set thumbnail
     * @param string $thumbnail
     * @return CategoryInterface
     */
    public function setThumbnail($thumbnail)
    {
        return $this->setData(self::THUMBNAIL, $thumbnail);
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
    public function getCategoryName()
    {
        return $this->getData(self::CATEGORY_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setCategoryName($value)
    {
        return $this->setData(self::CATEGORY_NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function setSubCategories($value)
    {
        return $this->setData(self::SUB_CATEGORIES, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSubCategories()
    {
        return $this->getData(self::SUB_CATEGORIES);
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
    public function getHowToBuyBill()
    {
        return $this->getData(self::HOW_TO_BUY_BILL);
    }

    /**
     * @inheritDoc
     */
    public function setHowToBuyBill($howToBuy)
    {
        return $this->setData(self::HOW_TO_BUY_BILL, $howToBuy);
    }
}
