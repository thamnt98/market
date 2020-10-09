<?php

namespace SM\Help\Ui\Topic\Form\Source;

use Magento\Framework\Data\OptionSourceInterface;
use SM\Help\Api\QuestionRepositoryInterface;
use SM\Help\Model\ResourceModel\Question\CollectionFactory;

/**
 * Class TopicTree
 * @package SM\Help\Ui\Topic\Form\Source
 */
class Question implements OptionSourceInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    /**
     * @var \Magento\Framework\DB\Helper
     */
    private $dbHelper;
    /**
     * @var CollectionFactory
     */
    private $questionCollectionFactory;
    /**
     * @var QuestionRepositoryInterface
     */
    private $questionRepository;


    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\DB\Helper $dbHelper,
        CollectionFactory $questionCollectionFactory,
        QuestionRepositoryInterface $questionRepository
    ) {

        $this->request = $request;
        $this->dbHelper = $dbHelper;
        $this->questionCollectionFactory = $questionCollectionFactory;
        $this->questionRepository = $questionRepository;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }

    /**
     * @param int $parentId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getOptions()
    {
        $collection = $this->questionCollectionFactory->create();

        foreach ($collection as $item) {
            $data[] = [
                'label' => $item->getData('title'),
                'value' => $item->getData('question_id'),
                'optgroup' => ''
            ];
        }
        return $data;
    }
}
