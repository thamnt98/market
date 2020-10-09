<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Ilma Dinnia Alghani <ilma.dinnia@transdigital.co.id>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalog\Api;

use Trans\IntegrationCatalog\Api\Data\ProductAssociationInterface;

interface ProductAssociationRepositoryInterface
{


    /**
     * Save Data
     *
     * @param \Trans\IntegrationCatalog\Api\Data\ProductAssociationInterface $dataIntegrationCatalog
     * @return \Trans\IntegrationCatalog\Api\Data\ProductAssociationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function save(ProductAssociationInterface $dataIntegrationCatalog);

    /**
     * Delete data.
     *
     * @param \Trans\IntegrationCatalog\Api\Data\ProductAssociationInterface $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(ProductAssociationInterface $dataIntegrationCatalog);

    /**
     * Load Integration Product association by pim id.
     *
     * @param mixed $data
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadDataPromoByPromoId($data);
}
