<?php


namespace SM\MobileApi\Api\Data\Catalog\Product\Configurable;


interface AttributeInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ID = 'id';
    const CODE = 'code';
    const LABEL = 'label';
    const OPTIONS = 'options';
    const INPUT_TYPE = 'input_type';

    /**
     * Get attribute ID
     *
     * @return int
     */
    public function getId();

    /**
     * @param int $data
     * @return $this
     */
    public function setId($data);

    /**
     * Get attribute code
     *
     * @return string
     */
    public function getCode();

    /**
     * @param string $data
     * @return $this
     */
    public function setCode($data);

    /**
     * Get attribute label
     *
     * @return string
     */
    public function getLabel();

    /**
     * @param string $data
     * @return $this
     */
    public function setLabel($data);

    /**
     * @return string
     */
    public function getInputType();

    /**
     * @param string $type
     * @return $this
     */
    public function setInputType($type);

    /**
     * Get attribute options
     *
     * @return \SM\MobileApi\Api\Data\Catalog\Product\Configurable\AttributeOptionInterface[]
     */
    public function getOptions();

    /**
     * @param \SM\MobileApi\Api\Data\Catalog\Product\Configurable\AttributeOptionInterface[] $data
     * @return $this
     */
    public function setOptions($data);
}
