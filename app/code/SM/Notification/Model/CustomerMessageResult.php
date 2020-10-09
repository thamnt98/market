<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 14 2020
 * Time: 4:18 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model;

use Magento\Framework\Api\SearchResultsInterface;
use SM\Notification\Api\CustomerMessageResultInterface;

class CustomerMessageResult extends \Magento\Framework\Api\SearchResults implements
    \SM\Notification\Api\CustomerMessageResultInterface
{
}
