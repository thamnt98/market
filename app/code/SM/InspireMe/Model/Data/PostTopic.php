<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Model\Data;

use Magento\Framework\Model\AbstractExtensibleModel;
use SM\InspireMe\Api\Data\PostTopicInterface;

class PostTopic extends AbstractExtensibleModel implements PostTopicInterface{

    /**
     * @inheritdoc
     */
    public function getTopicId()
    {
        return $this->getData(self::TOPIC_ID);
    }

    /**
     * @inheritdoc
     */
    public function setTopicId($data)
    {
        return $this->setData(self::TOPIC_ID,$data);
    }

    /**
     * @inheritdoc
     */
    public function getTopicName()
    {
        return $this->getData(self::TOPIC_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setTopicName($data)
    {
        return $this->setData(self::TOPIC_NAME,$data);
    }

}