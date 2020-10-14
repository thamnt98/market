<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Api;

use Magento\Framework\Exception\InputException;

/**
 * Interface for customers Integration.
 * @api
 */
interface CustomerIntegrationInterface
{

    CONST REQUIRED_FIELD_1 = "data";
    CONST DEFAULT_WEBSITE_ID= 1;
    CONST DEFAULT_STORE_ID= 1;
    /**
     * @return Trans\Customer\Api\Data\CustomerIntegrationResponseInterface
     */
    public function CreateNewCustomer();


}
