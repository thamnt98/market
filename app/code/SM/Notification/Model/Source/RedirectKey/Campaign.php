<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: October, 15 2020
 * Time: 3:16 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model\Source\RedirectKey;

class Campaign implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \SM\TodayDeal\Model\ResourceModel\Post\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var null|array
     */
    protected $options = null;

    /**
     * Order constructor.
     *
     * @param \SM\TodayDeal\Model\ResourceModel\Post\CollectionFactory $collectionFactory
     */
    public function __construct(
        \SM\TodayDeal\Model\ResourceModel\Post\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        if (is_null($this->options)) {
            $this->options = [];
            /** @var \SM\TodayDeal\Model\ResourceModel\Post\Collection $coll */
            $coll = $this->collectionFactory->create();
            $coll->addFieldToFilter('is_active', 1);

            /** @var \SM\TodayDeal\Model\Post $campaign */
            foreach ($coll->getItems() as $campaign) {
                $this->options[] = [
                    'label' => $campaign->getTitle(),
                    'value' => $campaign->getId(),
                ];
            }
        }

        return $this->options;
    }
}
