<?php

namespace SM\MobileApi\Api\Data\Catalog\Product;

/**
 * Interface for storing attribute infomation
 */
interface OptionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const OPTION_ID = 'option_id';
    const TITLE = 'title';
    const TYPE = 'type';
    const IS_REQUIRE = 'is_require';
    const SORT_ORDER = 'sort_order';
    const ADDITIONAL_FIELDS = 'additional_fields';

    /**
     * Get option ID
     *
     * @return int
     */
    public function getOptionId();

    /**
     * @param int $data
     *
     * @return $this
     */
    public function setOptionId($data);

    /**
     * Get option title
     *
     * @return string
     */
    public function getTitle();

    /**
     * @param string $data
     *
     * @return $this
     */
    public function setTitle($data);

    /**
     * Get option input type
     *
     * @return string
     */
    public function getType();

    /**
     * @param string $data
     *
     * @return $this
     */
    public function setType($data);

    /**
     * Get option is required
     *
     * @return int
     */
    public function getIsRequire();

    /**
     * @param int $data
     *
     * @return $this
     */
    public function setIsRequire($data);

    /**
     * Get option position
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $data
     *
     * @return $this
     */
    public function setSortOrder($data);

    /**
     * Get option values
     *
     * @return \SM\MobileApi\Api\Data\Catalog\Product\Option\ValueInterface[]
     */
    public function getAdditionalFields();

    /**
     * @param \SM\MobileApi\Api\Data\Catalog\Product\Option\ValueInterface[] $data
     *
     * @return $this
     */
    public function setAdditionalFields($data);
}
