<?php
namespace SM\FlashSale\Api\Data;

interface FlashSaleDateInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const DATE_START = 'date_start';
    const DATE_END = 'date_end';
    const DATE_START_CONVERTED = 'date_start_converted';
    const DATE_END_CONVERTED = 'date_end_converted';

    /**
     * @return string
     */
    public function getDateStart();

    /**
     * @param string $value
     * @return $this
     */
    public function setDateStart($value);

    /**
     * @return string
     */
    public function getDateEnd();

    /**
     * @param string $value
     * @return $this
     */
    public function setDateEnd($value);

    /**
     * @return string
     */
    public function getDateStartConverted();

    /**
     * @param string $value
     * @return $this
     */
    public function setDateStartConverted($value);

    /**
     * @return string
     */
    public function getDateEndConverted();

    /**
     * @param string $value
     * @return $this
     */
    public function setDateEndConverted($value);
}
