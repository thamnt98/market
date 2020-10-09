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

class CreateTicketResponse extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\JiraService\Api\Data\CreateTicketResponseInterface
{
    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData('id', $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_get('id');
    }

    /**
     * {@inheritdoc}
     */
    public function setKey($key)
    {
        return $this->setData('key', $key);
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->_get('key');
    }

    /**
     * {@inheritdoc}
     */
    public function setSelfLink($url)
    {
        return $this->setData('self', $url);
    }

    /**
     * {@inheritdoc}
     */
    public function getSelfLink()
    {
        return $this->_get('self');
    }

    /**
     * {@inheritdoc}
     */
    public function setIsError($status)
    {
        return $this->setData('error', $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsError()
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
