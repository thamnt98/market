<?php
/**
 * @category Trans
 * @package  Trans_CatalogStock
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogStock\Api;

use \Trans\IntegrationCatalogStock\Api\Data\IntegrationCheckStockRequestInterface ;

interface IntegrationCheckStockInterface
{
    /**
     * @return Trans\IntegrationCatalogStock\Api\Data\IntegrationCheckStockResponseInterface
     */
    public function execute();
}