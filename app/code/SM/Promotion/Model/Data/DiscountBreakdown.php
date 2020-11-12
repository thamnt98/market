<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: July, 16 2020
 * Time: 4:51 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Model\Data;

class DiscountBreakdown extends \Amasty\Rules\Model\DiscountBreakdownLine implements
    \SM\Promotion\Api\Data\DiscountBreakdownInterface
{
    /**
     * @return int
     */
    public function getId()
    {
        return $this->_get(self::KEY_RULE_ID);
    }

    /**
     * @param int $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->setData(self::KEY_RULE_ID, $id);

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->_get(self::KEY_COUPON_CODE);
    }

    /**
     * @param string $code
     *
     * @return self
     */
    public function setCode($code)
    {
        $this->setData(self::KEY_COUPON_CODE, $code);

        return $this;
    }

    /**
     * @return float
     */
    public function getShippingDiscount()
    {
        return $this->_get(self::SHIPPING_DISCOUNT);
    }

    /**
     * @param float $amount
     *
     * @return self
     */
    public function setShippingDiscount($amount)
    {
        $this->setData(self::SHIPPING_DISCOUNT, $amount);

        return $this;
    }

    /**
     * @return float
     */
    public function getItemsDiscount()
    {
        return $this->_get(self::ITEMS_DISCOUNT);
    }

    /**
     * @param float $amount
     *
     * @return self
     */
    public function setItemsDiscount($amount)
    {
        $this->setData(self::ITEMS_DISCOUNT, $amount);

        return $this;
    }
}
