<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Integration\Api\Data;
 
use Magento\Framework\Api\SearchResultsInterface;
 
interface IntegrationChannelSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Trans\Integration\Api\Data\IntegrationChannelInterface[]
     */
    public function getItems();
 
    /**
     * @param \Trans\Integration\Api\Data\IntegrationChannelInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}