<?php

namespace SM\MobileApi\Api\Data\ProductInstallation;

/**
 * Interface for storing review data
 */
interface InstallationInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const STATUS = 'status';
    const TOOLTIP = 'tooltip';

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus();

    /**
     * @param int $value
     * @return $this
     */
    public function setStatus($value);

    /**
     * Get status
     *
     * @return string
     */
    public function getTooltip();

    /**
     * @param string $value
     * @return $this
     */
    public function setTooltip($value);

}
