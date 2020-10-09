<?php

/**
 * @category SM
 * @package SM_Brand
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author      Chinhvd <chinhvd@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Brand\Model\Ui\Source;

use SM\TodayDeal\Model\ResourceModel\Post\CollectionFactory;

class ListCampaign implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * RelatedCampaigns constructor.
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        /** @var \SM\TodayDeal\Model\ResourceModel\Post\Collection $collection */
        $collection = $this->collectionFactory->create();
        $result = [];

        /** @var \SM\TodayDeal\Model\Post  $post */
        foreach ($collection as $post) {
            $result[] = [
                'value' => $post->getId(),
                'label' => $post->getTitle()
            ];
        }
        array_unshift($result, ['value' => '', 'label' => __('Please select a campaign page.')]);

        return $result;
    }
}
