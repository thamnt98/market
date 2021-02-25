<?php
/**
 * @category Trans
 * @package  Trans_Mepay
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Mepay\Model\CartRecovery;

use Magento\Framework\DataObject;
use Trans\Mepay\Api\Data\CartRecoveryResultInterface;


class Result extends DataObject implements CartRecoveryResultInterface
{
    /**
     * Set message
     * @param string $message
     * @return void
     */
    public function setMessage(string $message)
    {
        $this->setData(CartRecoveryResultInterface::MESSAGE, $message);
    }

    /**
     * Get message
     * @return string
     */
    public function getMessage()
    {
        return $this->_getData(CartRecoveryResultInterface::MESSAGE);
    }

    /**
     * Set status
     * @param string $status
     * @return void
     */
    public function setStatus(string $status)
    {
        $this->setData(CartRecoveryResultInterface::STATUS, $status);
    }

    /**
     * Get status
     * @return string
     */
    public function getStatus()
    {
        return $this->_getData(CartRecoveryResultInterface::STATUS);
    }
}
