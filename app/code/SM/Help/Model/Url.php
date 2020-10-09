<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface as MagentoUrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use SM\Help\Api\Data\QuestionInterface;
use SM\Help\Api\Data\TopicInterface;
use SM\Help\Model\TopicFactory;
use SM\Help\Model\PageFactory;

/**
 * Class Url
 * @package SM\Help\Model
 */
class Url
{
    const QUESTION_KEY = 'question';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \SM\Help\Model\Config
     */
    protected $config;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var MagentoUrlInterface
     */
    protected $urlManager;

    /**
     * @var \SM\Help\Model\TopicFactory
     */
    protected $topicFactory;

    /**
     * @var ResourceModel\Topic\CollectionFactory
     */
    protected $topicCollectionFactory;

    /**
     * @var ResourceModel\Question\CollectionFactory
     */
    protected $questionCollectionFactory;

    /**
     * @var \Magento\Store\Api\Data\StoreInterface
     */
    protected $currentStore;

    /**
     * Url constructor.
     * @param StoreManagerInterface $storeManager
     * @param \SM\Help\Model\Config $config
     * @param ScopeConfigInterface $scopeConfig
     * @param \SM\Help\Model\TopicFactory $topicFactory
     * @param ResourceModel\Topic\CollectionFactory $topicCollectionFactory
     * @param ResourceModel\Question\CollectionFactory $questionCollectionFactory
     * @param MagentoUrlInterface $urlManager
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Config $config,
        ScopeConfigInterface $scopeConfig,
        TopicFactory $topicFactory,
        \SM\Help\Model\ResourceModel\Topic\CollectionFactory $topicCollectionFactory,
        \SM\Help\Model\ResourceModel\Question\CollectionFactory $questionCollectionFactory,
        MagentoUrlInterface $urlManager
    ) {
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->scopeConfig = $scopeConfig;
        $this->urlManager = $urlManager;
        $this->topicFactory = $topicFactory;
        $this->topicCollectionFactory = $topicCollectionFactory;
        $this->questionCollectionFactory = $questionCollectionFactory;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->urlManager->getUrl($this->config->getBaseRoute());
    }

    /**
     * @param string $route
     * @param $type
     * @param array $urlParams
     * @return string
     */
    protected function getUrl($route, $type, $urlParams = [])
    {
        $url = $this->urlManager->getUrl($this->config->getBaseRoute() . $route, $urlParams);

        if ($type == 'topic' && $this->getTopicUrlSuffix()) {
            $url = $this->addSuffix($url, $this->getTopicUrlSuffix());
        }

        if ($type == self::QUESTION_KEY && $this->config->getQuestionUrlSuffix()) {
            $url = $this->addSuffix($url, $this->config->getQuestionUrlSuffix());
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getTopicUrlSuffix()
    {
        return $this->config->getTopicUrlSuffix();
    }

    /**
     * @return string
     */
    public function getQuestionUrlSuffix()
    {
        return $this->config->getQuestionUrlSuffix();
    }

    /**
     * @param \SM\Help\Model\Topic $topic
     * @param array $urlParams
     * @return string
     */
    public function getTopicUrl($topic, $urlParams = [])
    {
        return $this->getUrl('/' . $topic->getUrlKey(), 'topic', $urlParams);
    }

    /**
     * @return string
     */
    public function getQuestionBaseUrl()
    {
        return $this->urlManager->getUrl($this->config->getBaseRoute() . '/' . self::QUESTION_KEY);
    }

    /**
     * @param \SM\Help\Model\Question $question
     * @param array $urlParams
     * @return string
     */
    public function getQuestionUrl($question, $urlParams = [])
    {
        return $this->getUrl('/' . self::QUESTION_KEY . '/' . $question->getUrlKey(), self::QUESTION_KEY, $urlParams);
    }

    /**
     * @param string $url
     * @param string $suffix
     * @return string
     */
    private function addSuffix($url, $suffix)
    {
        $parts    = explode('?', $url, 2);
        $parts[0] = rtrim($parts[0], '/') . $suffix;

        return implode('?', $parts);
    }

    /**
     * @param array $urlParams
     *
     * @return string
     */
    public function getSearchUrl($urlParams = [])
    {
        return $this->getUrl('/search/', 'search', $urlParams);
    }

    /**
     * @param string $pathInfo
     *
     * @return bool|DataObject
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function match($pathInfo)
    {
        $identifier = trim($pathInfo, '/');
        $parts      = explode('/', $identifier);

        if (count($parts) >= 1) {
            $parts[count($parts) - 1] = $this->trimSuffix($parts[count($parts) - 1]);
        }

        if ($parts[0] != $this->config->getBaseRoute()) {
            return false;
        }

        if (count($parts) > 1) {
            unset($parts[0]);
            $parts  = array_values($parts);
            $urlKey = implode('/', $parts);
            $urlKey = urldecode($urlKey);
            $urlKey = $this->trimSuffix($urlKey);
        } else {
            $urlKey = '';
        }

        if ($urlKey == '') {
            return new DataObject([
                'module_name'     => 'help',
                'controller_name' => 'index',
                'action_name'     => 'index',
                'params'          => [],
            ]);
        }

        if ($parts[0] == 'search') {
            return new DataObject([
                'module_name'     => 'help',
                'controller_name' => 'search',
                'action_name'     => 'result',
                'params'          => [],
            ]);
        }

        if ($parts[0] == 'question') {
            $question = $this->questionCollectionFactory->create()
                ->addStoreFilter($this->getStoreId())
                ->addFieldToFilter('url_key', $parts[1])
                ->getFirstItem();

            if ($question->getId()) {
                return new DataObject([
                    'module_name'     => 'help',
                    'controller_name' => 'view',
                    'action_name'     => 'question',
                    'params'          => [QuestionInterface::ID => $question->getId()],
                ]);
            }
        }

        $topic = $this->topicCollectionFactory->create()
            ->addStoreFilter()
            ->addFieldToFilter('url_key', $urlKey)
            ->getFirstItem();

        if ($topic->getId()) {
            return new DataObject([
                'module_name'     => 'help',
                'controller_name' => 'view',
                'action_name'     => 'topic',
                'params'          => [TopicInterface::ID => $topic->getId()],
            ]);
        }

        return false;
    }

    /**
     * Return url without suffix
     * @param string $key
     * @return string
     */
    protected function trimSuffix($key)
    {
        $suffix = $this->config->getTopicUrlSuffix();
        //user can enter .html or html suffix
        if ($suffix != '' && $suffix[0] != '.') {
            $suffix = '.' . $suffix;
        }

        $key = str_replace($suffix, '', $key);

        return $key;
    }

    /**
     * @return int
     */
    protected function getStoreId()
    {
        try {
            if (!$this->currentStore) {
                $this->currentStore =  $this->storeManager->getStore();
            }
            return $this->currentStore->getId();
        } catch (NoSuchEntityException $e) {
            return 0;
        }
    }

    /**
     * @return string
     */
    public function getCurrentStoreCode()
    {
        try {
            if (!$this->currentStore) {
                $this->currentStore =  $this->storeManager->getStore();
            }
            return $this->currentStore->getCode();
        } catch (NoSuchEntityException $e) {
            return '';
        }
    }
}
