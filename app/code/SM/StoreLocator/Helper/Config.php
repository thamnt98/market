<?php

declare(strict_types=1);

namespace SM\StoreLocator\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Config
 * @package SM\StoreLocator\Helper
 */
class Config
{
    const STORE_LOCATOR_MAXIMUM_DISTANCE = 'store_locator/general/max_distance';
    const DELETE_UPLOADED_FILE_XML_PATH = 'store_locator/general/delete_uploaded_file';
    const NUMBER_LOCATION_DISPLAYED_XML_PATH = 'store_locator/general/number_location_displayed';
    const GOOGLE_API_KEY_XML_PATH = 'cms/pagebuilder/google_maps_api_key';
    const NUMBER_LOCATION_DISPLAYED_DEFAULT = 1;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return float
     */
    public function getMaximumDistance(): float
    {
        return (float) $this->scopeConfig->getValue(self::STORE_LOCATOR_MAXIMUM_DISTANCE) ?? 0;
    }

    /**
     * @return bool
     */
    public function getDeleteUploadedFile(): bool
    {
        return (bool) $this->scopeConfig->getValue(self::DELETE_UPLOADED_FILE_XML_PATH) ?? false;
    }

    /**
     * @return int
     */
    public function getNumberLocationDisplayedConfiguration(): int
    {
        return (int) $this->scopeConfig->getValue(self::NUMBER_LOCATION_DISPLAYED_XML_PATH) ?? self::NUMBER_LOCATION_DISPLAYED_DEFAULT;
    }

    /**
     * @return string|null
     */
    public function getGoogleApiKey()
    {
        return (string) $this->scopeConfig->getValue(self::GOOGLE_API_KEY_XML_PATH) ?? null;
    }
}
