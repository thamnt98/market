<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Api\Data;

/**
 * Interface CategoryInterface
 * @package SM\DigitalProduct\Api\Data
 */
interface CategoryInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const THUMBNAIL = "thumbnail";
    const TYPE = "type";
    const CATEGORY_NAME = "category_name";
    const SUB_CATEGORIES = "sub_categories";
    const CATEGORY_ID = "category_id";
    const MAGENTO_CATEGORY_IDS = "magento_category_ids";
    const HOW_TO_BUY = 'how_to_buy';
    const HOW_TO_BUY_BILL = 'how_to_buy_bill';

    /**
     * @param string $value
     * @return \SM\DigitalProduct\Api\Data\CategoryInterface
     */
    public function setType($value);

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getCategoryName();

    /**
     * @param string $value
     * @return $this
     */
    public function setCategoryName($value);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \SM\DigitalProduct\Api\Data\CategoryExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \SM\DigitalProduct\Api\Data\CategoryExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \SM\DigitalProduct\Api\Data\CategoryExtensionInterface $extensionAttributes
    );

    /**
     * Get thumbnail
     * @return string|null
     */
    public function getThumbnail();

    /**
     * Set thumbnail
     * @param string $thumbnail
     * @return \SM\DigitalProduct\Api\Data\CategoryInterface
     */
    public function setThumbnail($thumbnail);

    /**
     * @param \SM\DigitalProduct\Api\Data\SubCategoryDataInterface[] $value
     * @return $this
     */
    public function setSubCategories($value);

    /**
     * @return \SM\DigitalProduct\Api\Data\SubCategoryDataInterface[]
     */
    public function getSubCategories();

    /**
     * Get how_to_buy
     * @return string
     */
    public function getHowToBuy();

    /**
     * Set how_to_buy
     * @param string $howToBuy
     * @return $this
     */
    public function setHowToBuy($howToBuy);

    /**
     * Get how_to_buy
     * @return string|null
     */
    public function getHowToBuyBill();

    /**
     * Set how_to_buy
     * @param string $howToBuy
     * @return $this
     */
    public function setHowToBuyBill($howToBuy);
}
