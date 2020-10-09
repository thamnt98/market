<?php

namespace SM\MobileApi\Api\Data\Store;

/**
 * Interface StoreViewInterface
 *
 * @package SM\MobileApi\Api\Data\Store
 */
interface StoreViewInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const STORE_ID = 'store_id';
    const STORE_CODE = 'store_code';
    const LANGUAGE = 'language';

    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set store id
     *
     * @param int $id
     * @return $this
     */
    public function setStoreId($id);

    /**
     * Get store code
     *
     * @return string
     */
    public function getStoreCode();

    /**
     * Set store code
     *
     * @param string $code
     * @return $this
     */
    public function setStoreCode($code);

    /**
     * Get store language
     *
     * @return string
     */
    public function getLanguage();

    /**
     * Set store language
     *
     * @param string $language
     * @return $this
     */
    public function setLanguage($language);

}
