<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 15 2020
 * Time: 10:10 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model\Data\Queue;

class Email extends AbstractData implements \SM\Notification\Api\Data\Queue\EmailInterface
{
    public function setTemplateId($id)
    {
        $this->setData('template_id', $id);

        return $this;
    }

    public function getTemplateId()
    {
        return $this->getData('template_id');
    }

    public function setParams($params)
    {
        $this->setData('params', $params);

        return $this;
    }

    public function getParams()
    {
        return $this->getData('params') ?? [];
    }
}
