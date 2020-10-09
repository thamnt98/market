<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_InspireMe
 *
 * Date: April, 17 2020
 * Time: 2:13 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\InspireMe\Api;

interface SearchQueryInterface
{
    /**
     * @param string $str
     *
     * @return \SM\InspireMe\Api\Data\PostListingInterface[]
     */
    public function query($str);
}
