<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 15 2020
 * Time: 10:03 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model\Data\Queue;

abstract class AbstractData extends \Magento\Framework\DataObject implements
    \SM\Notification\Api\Data\Queue\GeneralInterface
{
    public function setEvent($event)
    {
        $this->setData('event', $event);

        return $this;
    }

    public function getEvent()
    {
        return $this->getData('event');
    }

    public function setCustomerId($id)
    {
        $this->setData('customer_id', $id);

        return $this;
    }

    public function getCustomerId()
    {
        return $this->getData('customer_id');
    }

    public function setMessageId($id)
    {
        $this->setData('message_id', $id);

        return $this;
    }

    public function getMessageId()
    {
        return $this->getData('message_id');
    }

    public function setId($id)
    {
        $this->setData('id', $id);

        return $this;
    }

    public function getId()
    {
        return $this->getData('id');
    }
}
