<?php

namespace SM\MobileApi\Api\Data\Catalog\Product\BundleProduct;

use Magento\Framework\Api\ExtensibleDataInterface;

interface ProductOptionsInterface extends ExtensibleDataInterface
{
    const OPTION_ID = 'option_id';
    const PARENT_ID = 'parent_id';
    const REQUIRED = 'required';
    const POSITION = 'position';
    const TYPE = 'type';
    const DEFAULT_TITLE = 'default_title';
    const TITLE = 'title';
    const SELECTIONS = 'selections';

    /**
     * @return mixed
     */
    public function getOptionId();

    /**
     * @param int $data
     * @return mixed
     */
    public function setOptionId($data);
    /**
     * Get parent ID
     *
     * @return int
     */
    public function getParentId();

    /**
     * @param int $data
     * @return $this
     */
    public function setParentId($data);
    /**
     * Get check required
     *
     * @return int
     */
    public function getRequired();

    /**
     * @param int $data
     * @return $this
     */
    public function setRequired($data);
    /**
     * Get position
     *
     * @return int
     */
    public function getPosition();

    /**
     * @param int $data
     * @return $this
     */
    public function setPosition($data);

    /**
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * @param string $data
     * @return $this
     */
    public function setType($data);
    /**
     * Get default title
     *
     * @return string
     */
    public function getDefaultTitle();

    /**
     * @param string $data
     * @return $this
     */
    public function setDefaultTitle($data);

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * @param string $data
     * @return $this
     */
    public function setTitle($data);

    /**
     * Get product of options
     *
     * @return \SM\MobileApi\Api\Data\Catalog\Product\BundleProduct\ProductItemsInterface[]
     */
    public function getSelections();

    /**
     * @param \SM\MobileApi\Api\Data\Catalog\Product\BundleProduct\ProductItemsInterface[] $data
     * @return $this
     */
    public function setSelections($data);
}
