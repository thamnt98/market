<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2020 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Plugin\CatalogImportExport\Model\Import\Product;

use Magento\CatalogImportExport\Model\Import\Product\SkuProcessor as MageSkuProcessor;

/**
 * Class SkuProcessor
 */
class SkuProcessor
{
    /**
     * @var Magento\Catalog\Model\ResourceModel\Product
     */
    protected $productResource;

    /**
     * @param Magento\Catalog\Model\ResourceModel\Product
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product $productResource
    ) {
        $this->productResource = $productResource;
    }

    /**
     * get new sku
     *
     * @param MageSkuProcessor $subject
     * @param callable $proceed
     * @param string $sku
     */
    public function aroundGetNewSku(MageSkuProcessor $subject, callable $proceed, $sku = null)
    {
        $newSku = $proceed($sku);

        if($newSku === null) {
            $connection = $this->productResource->getConnection();
            $tableName = $connection->getTableName('catalog_product_entity');

            $query = $connection->select();
            $query->from(
                $tableName,
                ['*']
            )->where('sku = ?', $sku);

            $data = $connection->fetchRow($query);

            if($data) {
                return $data;
            }
        }

        return $newSku;
    }

}
