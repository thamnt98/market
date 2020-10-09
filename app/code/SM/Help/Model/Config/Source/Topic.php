<?php
/**
 * @category  SM
 * @package   SM_Catalog
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author    Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright 2020 Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);

namespace SM\Help\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use SM\Help\Api\Data\TopicInterface;
use SM\Help\Model\ResourceModel\Topic\CollectionFactory;
use SM\Help\Api\TopicRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class Topic
 * @package SM\Help\Model\Config\Source
 */
class Topic implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var TopicRepositoryInterface
     */
    protected $topicRepository;

    /**
     * Topic constructor.
     * @param CollectionFactory $collectionFactory
     * @param TopicRepositoryInterface $topicRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        TopicRepositoryInterface $topicRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->topicRepository = $topicRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $result = [];

        $sourceTopicCriteria = $this->searchCriteriaBuilder
            ->addFilter(TopicInterface::STATUS, 1)
            ->create();

        try {
            $sourcetopicData = $this->topicRepository->getList($sourceTopicCriteria)->getItems();
            if (is_array($sourcetopicData) && !empty($sourcetopicData)) {
                /** @var \SM\Help\Model\Topic $topic */
                foreach ($sourcetopicData as $topic) {
                    $result[] = [
                        'value' => $topic->getId(),
                        'label' => $topic->getName(),
                    ];
                }
            }
        } catch (\Exception $e) {
            return [];
        }

        return $result;
    }
}
