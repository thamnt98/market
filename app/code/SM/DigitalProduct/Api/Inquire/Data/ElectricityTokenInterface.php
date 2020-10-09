<?php
/**
 * Class ElectricityTokenInterface
 * @package SM\DigitalProduct\Api\Inquire\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Api\Inquire\Data;

interface ElectricityTokenInterface extends ResponseDataInterface
{
    const CUSTOMER_ID = 'customer_id';
    const MATERIAL_NUMBER = 'material_number';
    const NAME = 'subscriber_name';
    const POWER = 'power';

    /**
     * @return string
     */
    public function getCustomerId();

    /**
     * @return int
     */
    public function getMaterialNumber();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getPower();
}
