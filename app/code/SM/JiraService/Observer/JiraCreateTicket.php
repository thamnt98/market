<?php
/**
 * Class JiraCreateTicket
 * @package SM\JiraSearvice\Observer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\JiraService\Observer;

use Magento\Framework\Event\Observer;

class JiraCreateTicket implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \SM\JiraService\Api\JiraRepositoryInterface
     */
    private $jiraRepository;

    /**
     * @var SM\Help\Model\ResourceModel\Topic\CollectionFactory
     */
    private $topicCollectionFactory;

    /**
     * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    private $sourceRepository;

    /**
     * JiraCreateTicket constructor.
     * @param \SM\JiraService\Api\JiraRepositoryInterface $jiraRepository
     * @param \SM\Help\Model\ResourceModel\Topic\CollectionFactory $topicCollectionFactory
     * @param \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
     */
    public function __construct(
        \SM\JiraService\Api\JiraRepositoryInterface $jiraRepository,
        \SM\Help\Model\ResourceModel\Topic\CollectionFactory $topicCollectionFactory,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
    ) {
        $this->jiraRepository = $jiraRepository;
        $this->topicCollectionFactory = $topicCollectionFactory;
        $this->sourceRepository = $sourceRepository;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $data = $observer->getData();
        if (isset($data[0])) {
            $dataPost = $data[0]['data'];
        } else {
            $dataPost = $data['data'];
        }

        if ($dataPost['store']) {
            try {
                $store = $this->sourceRepository->get($dataPost['store']);
                $dataPost['store_code'] = $dataPost['store'];
                $dataPost['store'] = $store->getName();
            } catch (\Exception $e) {
            }
        }

        if ($dataPost['category'] && is_numeric($dataPost['category'])) {
            $topic = $this->topicCollectionFactory->create()
                ->addStoreFilter()
                ->addFieldToFilter(\SM\Help\Api\Data\TopicInterface::LEVEL, 1)
                ->addFieldToFilter('main_table.topic_id', $dataPost['category'])->getFirstItem();
            $this->jiraRepository->createTicketFromEvent(
                $topic->getName(),
                $dataPost,
                $observer->getData('customer')
            );
        } else {
            $this->jiraRepository->createTicketFromEvent(
                __('General'),
                $dataPost,
                $observer->getData('customer')
            );
        }
    }
}