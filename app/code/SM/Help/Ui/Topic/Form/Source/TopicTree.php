<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Ui\Topic\Form\Source;

use Magento\Framework\Data\OptionSourceInterface;
use SM\Help\Api\TopicRepositoryInterface;
use SM\Help\Model\Topic;

/**
 * Class TopicTree
 * @package SM\Help\Ui\Topic\Form\Source
 */
class TopicTree implements OptionSourceInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \SM\Help\Model\ResourceModel\Topic\CollectionFactory
     */
    protected $topicCollectionFactory;

    /**
     * @var \Magento\Framework\DB\Helper
     */
    protected $dbHelper;

    /**
     * @var TopicRepositoryInterface
     */
    protected $topicRepository;

    /**
     * TopicTree constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\DB\Helper $dbHelper
     * @param TopicRepositoryInterface $topicRepository
     * @param \SM\Help\Model\ResourceModel\Topic\CollectionFactory $topicCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\DB\Helper $dbHelper,
        \SM\Help\Api\TopicRepositoryInterface $topicRepository,
        \SM\Help\Model\ResourceModel\Topic\CollectionFactory $topicCollectionFactory
    ) {
        $this->request = $request;
        $this->topicCollectionFactory = $topicCollectionFactory;
        $this->dbHelper = $dbHelper;
        $this->topicRepository = $topicRepository;
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray()
    {
        return [$this->getOptions(Topic::TREE_ROOT_ID)];
    }

    /**
     * @param int $parentId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getOptions($parentId)
    {
        $topic = $this->topicRepository->getById($parentId);

        $data = [
            'label' => $topic->getName(),
            'value' => $topic->getId(),
        ];

        $collection = $this->topicCollectionFactory->create()
            ->addFieldToFilter(Topic::PARENT_ID, $topic->getId())
            ->setOrder(Topic::POSITION, 'asc')
            ->addStoreFilter();

        /** @var Topic $item */
        foreach ($collection as $item) {
            $data['optgroup'][] = $this->getOptions($item->getId());
        }

        return $data;
    }
}
