<?php
/**
 * Class DeliveryReturn
 * @package SM\MobileApi\Model\Data\Product
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\MobileApi\Model\Data\Product;


use Magento\Framework\Api\AbstractSimpleObject;
use SM\MobileApi\Api\Data\Product\DeliveryReturnInterface;

class DeliveryReturn extends AbstractSimpleObject implements DeliveryReturnInterface
{
    /**
     * @inheritDoc
     */
    public function getTopicName()
    {
        return $this->_get(self::TOPIC_NAME);
    }

    /**
     * @param string $value
     * @return DeliveryReturnInterface
     */
    public function setTopicName($value)
    {
        $this->setData(self::TOPIC_NAME, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getChildQuestions()
    {
        return $this->_get(self::QUESTIONS);
    }

    /**
     * @param \SM\Help\Api\Data\QuestionInterface[] $value
     * @return DeliveryReturnInterface
     */
    public function setChildQuestions($value)
    {
        $this->setData(self::QUESTIONS, $value);
        return $this;
    }
}
