<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\IntegrationCatalog\Api\Data;
 
use Magento\Framework\Api\SearchResultsInterface;
 
interface IntegrationJobSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Trans\IntegrationCatalog\Api\Data\IntegrationJobInterface[]
     */
    public function getItems();
 
    /**
     * @param \Trans\IntegrationCatalog\Api\Data\IntegrationJobInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}