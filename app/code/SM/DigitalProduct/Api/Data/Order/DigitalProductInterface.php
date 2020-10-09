<?php
/**
 * Class DigitalProductInterface
 * @package SM\DigitalProduct\Api\Data\Order
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Api\Data\Order;

interface DigitalProductInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const SERVICE_TYPE = "service_type";
    const DIGITAL = "digital";
    const DIGITAL_TRANSACTION = "digital_transaction";

    /**
     * @return string
     */
    public function getServiceType();

    /**
     * @return \SM\DigitalProduct\Api\Data\DigitalInterface
     */
    public function getDigital();

    /**
     * @return \SM\DigitalProduct\Api\Data\DigitalTransactionInterface
     */
    public function getDigitalTransaction();
}
