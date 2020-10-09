<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Ui\Topic\Form;

use Mirasvit\Blog\Api\Data\PostInterface;
use SM\Help\Api\Data\TopicInterface;

/**
 * Class DataProvider
 * @package SM\Help\Ui\Topic\Form
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \SM\Help\Model\Config
     */
    protected $config;

    /**
     * DataProvider constructor.
     * @param \SM\Help\Model\ResourceModel\Topic\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \SM\Help\Model\Config $config
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        \SM\Help\Model\ResourceModel\Topic\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \SM\Help\Model\Config $config,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->config = $config;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $listTopic = $this->collection->addStoreFilter()->getItems();
        $this->loadedData = array();

        /** @var \SM\Help\Model\Topic $topic */
        foreach ($listTopic as $topic) {
            if ($topic && $topic->getId()) {
                $this->loadedData[$topic->getId()] = $topic->getData();
                if ($topic->getImage() && file_exists($this->config->getMediaPath($topic->getImage()))) {
                    $this->loadedData[$topic->getId()][TopicInterface::IMAGE] = [
                        [
                            'name' => $topic->getImage(),
                            'url'  => $this->config->getMediaUrl($topic->getImage()),
                            'size' => filesize($this->config->getMediaPath($topic->getImage())),
                            'type' => 'image',
                        ],
                    ];
                } else {
                    $this->loadedData[$topic->getId()][TopicInterface::IMAGE] = null;
                }
            }
        }

        $data = $this->dataPersistor->get('sm_help_topic_persistor');
        if (!empty($data)) {
            $topic = $this->collection->getNewEmptyItem();
            $topic->setData($data);
            $this->loadedData[$topic->getId()] = $topic->getData();
            $this->dataPersistor->clear('sm_help_topic_persistor');
        }

        return $this->loadedData;
    }

    /**
     * @inheritdoc
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if ($filter->getField() == 'topic_id') {
            if (!is_null($filter->getValue())) {
                $this->getCollection()->getSelect()->where('main_table.topic_id = ' . $filter->getValue());
            }
        } else {
            parent::addFilter($filter);
        }
    }
}
