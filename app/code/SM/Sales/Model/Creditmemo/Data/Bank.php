<?php
/**
 * Class Bank
 * @package SM\Sales\Model\Creditmemo\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Sales\Model\Creditmemo\Data;

use Magento\Framework\Api\AbstractSimpleObject;
use SM\Sales\Api\Data\Creditmemo\BankInterface;

class Bank extends AbstractSimpleObject implements BankInterface
{
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    public function getCode()
    {
        return $this->_get(self::CODE);
    }
}
