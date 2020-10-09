<?php
/**
 * class CreateTicketResponse
 * @package SM\JiraService\Model\Data
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Dung Nguyen My <dungnm@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\JiraService\Model\Data;

class Response extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\JiraService\Api\Data\ResponseInterface
{
    /**
     * {@inheritdoc}
     */
    public function setError($status)
    {
        return $this->setData('error', $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getError()
    {
        return $this->_get('error');
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        return $this->setData('message', $message);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->_get('message');
    }
}
