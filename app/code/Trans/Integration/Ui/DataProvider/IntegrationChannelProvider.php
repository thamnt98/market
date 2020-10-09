<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Ui\DataProvider;

use Trans\Integration\Model\ResourceModel\IntegrationChannel\CollectionFactory;
use Trans\Integration\Api\Data\IntegrationChannelInterface;

/**
 * Class IntegrationChannelProvider
 */
class IntegrationChannelProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var array
     */
    protected $_loadedData;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Serialize\Serializer\Json $json,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->json = $json;
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }

        $items = $this->collection->getItems();
        
        foreach ($items as $item) {
            $data = $this->prepareData($item->getData());

            $this->_loadedData[$item->getId()] = $data;
        }

        return $this->_loadedData;
    }

    /**
     * prepare config data
     * 
     * @param array $config
     * @return array
     */
    protected function prepareData(array $config)
    {
        return $config;
    }
}
