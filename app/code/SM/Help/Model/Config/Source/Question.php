<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Question
 * @package SM\Help\Model\Config\Source
 */
class Question implements OptionSourceInterface
{
    /**
     * @var \SM\Help\Model\ResourceModel\Question\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Question constructor.
     * @param \SM\Help\Model\ResourceModel\Question\CollectionFactory $collectionFactory
     */
    public function __construct(
        \SM\Help\Model\ResourceModel\Question\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $result = [];

        $collection = $this->collectionFactory->create()
            ->setOrder(\SM\Help\Api\Data\QuestionInterface::CREATED_AT, 'desc');

        /** @var \SM\Help\Model\Question $question */
        foreach ($collection as $question) {
            $result[] = [
                'value' => $question->getId(),
                'label' => $question->getTitle(),
            ];
        }

        return $result;
    }
}
