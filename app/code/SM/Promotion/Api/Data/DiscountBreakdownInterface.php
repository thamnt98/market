<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: July, 16 2020
 * Time: 4:47 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Api\Data;

interface DiscountBreakdownInterface extends \Amasty\Rules\Api\Data\DiscountBreakdownLineInterface
{
    const KEY_COUPON_CODE = 'code';
    const KEY_RULE_ID     = 'id';
    const SHIPPING_DISCOUNT = 'shipping_discount';
    const ITEMS_DISCOUNT = 'items_discount';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return self
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     *
     * @return self
     */
    public function setCode($code);
}