<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCustomer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\IntegrationCustomer\Api\Data;
 
use Magento\Framework\Api\SearchResultsInterface;
 
interface IntegrationCustomerCentralSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Trans\IntegrationCustomer\Api\Data\IntegrationCustomerCentralInterface[]
     */
    public function getItems();
 
    /**
     * @param \Trans\IntegrationCustomer\Api\Data\IntegrationCustomerCentralInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}