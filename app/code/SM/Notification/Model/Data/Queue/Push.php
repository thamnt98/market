<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 15 2020
 * Time: 10:07 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model\Data\Queue;

use SM\Notification\Api\Data\Queue\GeneralInterface;

class Push extends AbstractData implements \SM\Notification\Api\Data\Queue\PushDeviceInterface
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

    public function setTitle($title)
    {
        $this->setData('title', $title);

        return $this;
    }

    public function getTitle()
    {
        return $this->getData('title') ?? '';
    }
}
