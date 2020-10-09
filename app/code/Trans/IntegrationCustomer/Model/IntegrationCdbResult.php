<?php
/**
 * @category Trans
 * @package  Trans_IntegrationCustomer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCustomer\Model;

use \Trans\IntegrationCustomer\Api\Data\IntegrationCdbResultInterface;

/**
 * CDB result data model.
 */
class IntegrationCdbResult extends \Magento\Framework\Api\AbstractSimpleObject implements IntegrationCdbResultInterface
{
    /**
     * {{@inheritdoc}}
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * {{@inheritdoc}}
     */
    public function setStatus($status)
    {
        $this->setData(self::STATUS, $status);
    }

    /**
     * {{@inheritdoc}}
     */
    public function getMessageId()
    {
        return $this->_get(self::MESSAGES_ID);
    }

    /**
     * {{@inheritdoc}}
     */
    public function setMessageId($messagesid)
    {
        $this->setData(self::MESSAGES_ID, $messagesid);
    }

    /**
     * {{@inheritdoc}}
     */
    public function getMessage()
    {
        return $this->_get(self::MESSAGES);
    }

    /**
     * {{@inheritdoc}}
     */
    public function setMessage($messages)
    {
        $this->setData(self::MESSAGES, $messages);
    }

    /**
     * {@inheritdoc}
     */
    public function generateMessageId()
    {
        $generator = "1357902468";
      
        $result = "";
      
        for ($i = 1; $i <= 10; $i++) {
            $result .= substr($generator, (rand()%(strlen($generator))), 1);
        }
      
        return $result;
    }
}
