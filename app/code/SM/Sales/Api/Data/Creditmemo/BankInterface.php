<?php
/**
 * Class BankInterface
 * @package SM\Sales\Api\Data\Creditmemo
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Sales\Api\Data\Creditmemo;

interface BankInterface
{
    const NAME = 'name';
    const CODE = 'code';

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getCode();
}
