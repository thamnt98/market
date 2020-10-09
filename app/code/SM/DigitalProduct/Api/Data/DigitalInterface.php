<?php
/**
 * Class DigitalInterface
 * @package SM\DigitalProduct\Api\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Api\Data;

interface DigitalInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const SERVICE_TYPE = 'service_type';
    const OPERATOR = 'operator';
    const PAY_UNTIL = 'pay_until';
    const METER_NUMBER = 'meter';
    const CUSTOMER_NAME = 'name';
    const CUSTOMER_ID = 'customer_id';
    const MOBILE_NUMBER = 'mobile_number';
    const MATERIAL_NUMBER = "material_number";
    const PERIOD = "period";
    const PRICE = "price";
    const BILL = "bill";
    const ADMIN_FEE = 'admin_fee';
    const INFORMATION = 'info';
    const POWER  = 'power';
    const INCENTIVE_AND_TAX_FEE  = 'incentive_and_tax_fee';
    const PRODUCT_NAME  = 'product_name';
    const SUBTOTAL  = 'subtotal';
    const PENALTY  = 'penalty';

    const DIGITAL_PRICE_FIELDS = [
        DigitalInterface::PRICE,
        DigitalInterface::ADMIN_FEE,
        DigitalInterface::SUBTOTAL,
        DigitalInterface::BILL,
        DigitalInterface::PENALTY,
        DigitalInterface::INCENTIVE_AND_TAX_FEE
    ];

    /**
     * @return string
     */
    public function getServiceType();

    /**
     * @param string $value
     * @return $this
     */
    public function setServiceType($value);

    /**
     * @return string
     */
    public function getOperator();

    /**
     * @param string $value
     * @return $this
     */
    public function setOperator($value);

    /**
     * @return string|null
     */
    public function getPayUntil();

    /**
     * @param string $value
     * @return $this
     */
    public function setPayUntil($value);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string $value
     * @return $this
     */
    public function setName($value);

    /**
     * @return string|null
     */
    public function getCustomerId();

    /**
     * @param string $value
     * @return $this
     */
    public function setCustomerId($value);

    /**
     * @return string|null
     */
    public function getMobileNumber();

    /**
     * @param string $value
     * @return $this
     */
    public function setMobileNumber($value);

    /**
     * @return string
     */
    public function getProductName();

    /**
     * @param string $value
     * @return $this
     */
    public function setProductName($value);

    /**
     * @return string|null
     */
    public function getPeriod();

    /**
     * @param string $value
     * @return $this
     */
    public function setPeriod($value);

    /**
     * @return string|null
     */
    public function getPower();

    /**
     * @param string $value
     * @return $this
     */
    public function setPower($value);

    /**
     * @return string|null
     */
    public function getMaterialNumber();

    /**
     * @param string $value
     * @return $this
     */
    public function setMaterialNumber($value);

    /**
     * @return string
     */
    public function getPrice();

    /**
     * @param string $value
     * @return $this
     */
    public function setPrice($value);

    /**
     * @return string|null
     */
    public function getBill();

    /**
     * @param string $value
     * @return $this
     */
    public function setBill($value);

    /**
     * @return string
     */
    public function getIncentiveAndTaxFee();

    /**
     * @param string $value
     * @return $this
     */
    public function setIncentiveAndTaxFee($value);

    /**
     * @return string
     */
    public function getAdminFee();

    /**
     * @param string $value
     * @return $this
     */
    public function setAdminFee($value);

    /**
     * @return string
     */
    public function getInfo();

    /**
     * @param string $value
     * @return $this
     */
    public function setInfo($value);

    /**
     * @return int
     */
    public function getSubtotal();

    /**
     * @param int $value
     * @return $this
     */
    public function setSubtotal($value);

    /**
     * @return string
     */
    public function getPenalty();

    /**
     * @param string $value
     * @return $this
     */
    public function setPenalty($value);
}
