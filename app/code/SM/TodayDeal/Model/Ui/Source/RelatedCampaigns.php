<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\TodayDeal\Model\Ui\Source;

/**
 * Class RelatedCampaigns
 * @package SM\TodayDeal\Model\Ui\Source
 */
class RelatedCampaigns implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \SM\TodayDeal\Model\ResourceModel\Post\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * RelatedCampaigns constructor.
     * @param \SM\TodayDeal\Model\ResourceModel\Post\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \SM\TodayDeal\Model\ResourceModel\Post\CollectionFactory $collectionFactory,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $currentId = $this->request->getParam('post_id');
        /** @var \SM\TodayDeal\Model\ResourceModel\Post\Collection $collection */
        $collection = $this->collectionFactory->create();

        if ($currentId) {
            $collection->addFieldToFilter(\SM\TodayDeal\Api\Data\PostInterface::POST_ID, ['neq' => $currentId]);
        }

        $result = [];

        /** @var \SM\TodayDeal\Model\Post  $post */
        foreach ($collection as $post) {
            $result[] = [
                'value' => $post->getId(),
                'label' => $post->getTitle()
            ];
        }

        return $result;
    }
}
