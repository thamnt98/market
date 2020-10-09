<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Api\Data;

/**
 * Interface PinProductInterface
 * @package SM\InspireMe\Api\Data
 */
interface PinProductInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    const SKU = 'sku';
    const NAME = 'name';
    const STATUS = 'status';
    const PRICE = 'price';
    const VISIBILITY = 'visibility';
    const TYPE_ID = 'type_id';

    /**
     * @return string
     */
    public function getSku();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return float
     */
    public function getPrice();

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @return int
     */
    public function getVisibility();

    /**
     * @return string
     */
    public function getTypeId();
}
