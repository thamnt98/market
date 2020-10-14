<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Integration\Model\Config\Source;

/**
 * Class BaseUrl
 */
class BaseUrl implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Trans\Integration\Model\ResourceModel\IntegrationChannel\CollectionFactory
     */
    protected $channelCollection;

    /**
     * @param \Trans\Integration\Model\ResourceModel\IntegrationChannel\CollectionFactory $channelCollection
     */
    public function __construct(
        \Trans\Integration\Model\ResourceModel\IntegrationChannel\CollectionFactory $channelCollection
    ) {
        $this->channelCollection = $channelCollection;
    }

    /**
     * get channel collection
     *
     * @return \Trans\Integration\Model\ResourceModel\IntegrationChannel\Collection
     */
    protected function getCollection()
    {
        return $this->channelCollection->create();
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $channels = $this->getCollection();
        $data = [['value' => '', 'label' => '-']];
        
        foreach ($channels as $channel) {
            $row['value'] = $channel->getId();
            $row['label'] = $channel->getUrl();
            $data[] = $row;
        }

        return $data;
    }
}
