<?php
/**
 * Class ElectricityToken
 * @package SM\DigitalProduct\Model\Api\Inquire\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Model\Api\Inquire\Data;

use SM\DigitalProduct\Api\Inquire\Data\ElectricityTokenInterface;
use SM\DigitalProduct\Api\Inquire\Data\ResponseDataInterface;

class ElectricityToken extends AbstractElectricity implements ElectricityTokenInterface
{
    /**
     * @inheritDoc
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function getMaterialNumber()
    {
        return $this->getData(self::MATERIAL_NUMBER);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function getPower()
    {
        return $this->getData(self::POWER);
    }

    /**
     * @inheritDoc
     */
    public function getAdminFee()
    {
        return $this->getData(self::ADMIN_FEE);
    }
}
