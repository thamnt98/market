<?php
/**
 * Class DeliveryReturnInterface
 * @package SM\MobileApi\Api\Data\Product
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\MobileApi\Api\Data\Product;

interface DeliveryReturnInterface
{
    const TOPIC_NAME = 'topic_name';
    const QUESTIONS = 'questions';

    /**
     * @return string
     */
    public function getTopicName();

    /**
     * Set Topic Name.
     *
     * @param string $value
     * @return $this
     */
    public function setTopicName($value);

    /**
     * Get Child Questions of current Topic with Store view
     *
     * @return \SM\Help\Api\Data\QuestionInterface[]
     */
    public function getChildQuestions();

    /**
     * Set Question list.
     *
     * @param  \SM\Help\Api\Data\QuestionInterface[] $value
     * @return $this
     */
    public function setChildQuestions($value);
}
