<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogPrice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 * @modify   J.P <jaka.pondan@ctcorpdigital.com>, Anan Fauzi <anan.fauzi@transdigital.co.id>
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogPrice\Api;

use Trans\IntegrationCatalogPrice\Api\Data\StorePriceInterface;

interface StorePriceRepositoryInterface
{
    /**
     * Retrieve data by id
     *
     * @param int $id
     * @return \Trans\IntegrationCatalogPrice\Api\Data\StorePriceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Save Data
     *
     * @param \Trans\IntegrationCatalogPrice\Api\Data\StorePriceInterface $data
     * @return \Trans\IntegrationCatalogPrice\Api\Data\StorePriceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function save(StorePriceInterface $data);

    /**
     * Delete data.
     *
     * @param \Trans\IntegrationCatalogPrice\Api\Data\StorePriceInterface $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(StorePriceInterface $data);

    /**
     * Load Integration Product by sku.
     *
     * @param mixed $sku
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadDataBySku($sku);

    /**
     * Load Integration Product by sku & Store.
     *
     * @param mixed $sku
     * @param mixed $store
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadDataBySkuNStore($sku = "", $store = "");

    /**
     * Load Integration Product by Store attr code.
     * @param mixed $code
     * @param mixed $store
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadDataByStoreAttrCode($code = "", $store = "");

    /**
     * Load Integration Product by Store.
     * @param mixed $store code
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadDataByStoreCode($store = "");

    /**
     * Get Inventory Store
     * @param string $store Store Code
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getInventoryStore($store);

    /**
     * Get Inventory Store collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getInventoryStoreCollection();
}
