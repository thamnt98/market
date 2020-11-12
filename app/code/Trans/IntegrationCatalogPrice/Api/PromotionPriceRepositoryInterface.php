<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalogPrice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   hadi <ashadi.sejati@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogPrice\Api;

use Trans\IntegrationCatalogPrice\Api\Data\PromotionPriceInterface;

interface PromotionPriceRepositoryInterface
{

    /**
     * Retrieve data by id
     *
     * @param int $id
     * @return \Trans\IntegrationCatalogPrice\Api\Data\PromotionPriceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Save Data
     *
     * @param \Trans\IntegrationCatalogPrice\Api\Data\PromotionPriceInterface $data
     * @return \Trans\IntegrationCatalogPrice\Api\Data\PromotionPriceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function save(PromotionPriceInterface $data);

    /**
     * Delete data.
     *
     * @param \Trans\IntegrationCatalogPrice\Api\Data\PromotionPriceInterface $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(PromotionPriceInterface $data);

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
    public function loadDataBySkuNStore($sku, $store);

    /**
     * Load Integration Product by promotype , discount type, mix match code.
     *
     * @param mixed $data
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadDataPromoFive($data);

     /**
     * Load Integration Product by promotype , discount type, mix match code , sku.
     *
     * @param mixed $data
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadDataPromoFiveCheck($data);

    /**
     * Load Integration Product by promo id.
     *
     * @param mixed $data
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadDataPromoByPromoId($data);

    /**
     * Load Integration Product by promo id and store code
     *
     * @param mixed $promotionid
     * @param mixed $storecode
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadDataPromoByPromoIdStoreCode($promotionid, $storecode);

    /**
     * Load Integration Product by Store attr code.
     * @param mixed $code
     * @param mixed $store
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadDataByStoreAttrCode($code, $store);

    /**
     * Load Integration Product by Store.
     * @param mixed $store code
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadDataByStoreCode($store);

    /**
     * Get Inventory Store
     * @param string $store Store Code
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getInventoryStore($store);
}
