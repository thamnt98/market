<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use SM\Help\Model\Topic;

/**
 * Class Help
 * @package SM\Help\Block
 */
abstract class Help extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \SM\Help\Model\ResourceModel\Topic\CollectionFactory
     */
    protected $topicCollectionFactory;

    /**
     * @var \SM\Help\Model\ResourceModel\Question\CollectionFactory
     */
    protected $questionCollectionFactory;

    /**
     * @var \SM\Help\Model\Config
     */
    protected $config;

    /**
     * @var \SM\Help\Api\TopicRepositoryInterface
     */
    protected $topicRepository;

    /**
     * Help constructor.
     * @param \SM\Help\Model\ResourceModel\Topic\CollectionFactory $topicCollectionFactory
     * @param \SM\Help\Model\ResourceModel\Question\CollectionFactory $questionCollectionFactory
     * @param \SM\Help\Api\TopicRepositoryInterface $topicRepository
     * @param \SM\Help\Model\Config $config
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \SM\Help\Model\ResourceModel\Topic\CollectionFactory $topicCollectionFactory,
        \SM\Help\Model\ResourceModel\Question\CollectionFactory $questionCollectionFactory,
        \SM\Help\Api\TopicRepositoryInterface $topicRepository,
        \SM\Help\Model\Config $config,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->topicCollectionFactory = $topicCollectionFactory;
        $this->questionCollectionFactory = $questionCollectionFactory;
        $this->config = $config;
        $this->topicRepository = $topicRepository;
    }


    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStore()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * @return \SM\Help\Api\Data\TopicInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCurrentTopic()
    {
        $topicId = $this->getRequest()->getParam('topic_id');
        return $this->topicRepository->getById($topicId);
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        try {
            return $this->_storeManager->getStore()->getId();
        } catch (NoSuchEntityException $e) {
            return 0;
        }
    }
}
