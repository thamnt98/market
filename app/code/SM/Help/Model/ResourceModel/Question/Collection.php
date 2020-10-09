<?php

/**
 * @category SM
 * @package SM_Help
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Chinhvd <chinhvd@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Help\Model\ResourceModel\Question;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SM\Help\Api\Data\QuestionInterface;

class Collection extends AbstractCollection
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'main_table.question_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sm_help_question_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'sm_help_question_collection';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('SM\Help\Model\Question', 'SM\Help\Model\ResourceModel\Question');
    }

    /**
     * @return $this|AbstractCollection|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()
            ->join(
                ['question_store' => 'sm_help_question_store'],
                'main_table.question_id = question_store.question_id',
                ['store_id']
            );

        return $this;
    }

    /**
     * @param int $storeId
     * @return Collection
     */
    public function addStoreFilter($storeId)
    {
        return $this->addFieldToFilter('store_id', ['in' => [0, $storeId]]);
    }

    /**
     * @param int $topicId
     * @return Collection
     */
    public function addTopicFilter($topicId)
    {
        return $this->addFieldToFilter(QuestionInterface::TOPIC_IDS, ['eq' => $topicId]);
    }

    /**
     * @return Collection
     */
    public function addVisibilityFilter()
    {
        return $this->addFieldToFilter(QuestionInterface::STATUS, ['eq' => QuestionInterface::STATUS_ENABLED]);
    }
}
