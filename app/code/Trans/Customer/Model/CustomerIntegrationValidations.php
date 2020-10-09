<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Model;

use Trans\Customer\Api\Data\CustomerIntegrationValidationsInterface;
use Magento\Framework\Exception\StateException;

/**
 * Class Response
 */
class CustomerIntegrationValidations implements CustomerIntegrationValidationsInterface
{
    /**
     * @param $array
     * @param $field
     * @throws StateException
     */
    public function fields($array = [], $field = "")
    {
        if (!is_array($array)) {
            throw new StateException(
                __(self::MSG_ERROR_JSON_ARRAY)
            );
        }
        $fields = explode(',', $field);
        foreach ($fields as $row) {
            if (!isset($array[$field])) {
                throw new StateException(
                    __(self::MSG_ERROR_REQUIRE_FIELD, $field)
                );

            }
        }
    }
}
