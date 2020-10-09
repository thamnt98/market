<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 15 2020
 * Time: 10:08 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model\Data\Queue;

class Sms extends AbstractData implements \SM\Notification\Api\Data\Queue\SmsInterface
{

    public function setContent($content)
    {
        $this->setData('content', $content);

        return $this;
    }

    public function getContent()
    {
        return $this->getData('content') ?? '';
    }
}
