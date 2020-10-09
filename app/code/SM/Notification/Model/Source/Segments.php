<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: October, 07 2020
 * Time: 3:04 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model\Source;

class Segments implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magento\CustomerSegment\Model\ResourceModel\Segment\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var array|null
     */
    protected $options = null;

    /**
     * Segments constructor.
     *
     * @param \Magento\CustomerSegment\Model\ResourceModel\Segment\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\CustomerSegment\Model\ResourceModel\Segment\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (is_null($this->options)) {
            $options = [];
            /** @var \Magento\CustomerSegment\Model\ResourceModel\Segment\Collection $coll */
            $coll = $this->collectionFactory->create();
            $coll->addFieldToFilter('is_active', ['eq' => 1]);

            /** @var \Magento\CustomerSegment\Model\Segment $segment */
            foreach ($coll->getItems() as $segment) {
                $options[] = [
                    'label' => $segment->getName(),
                    'value' => $segment->getId(),
                ];
            }

            $this->options = $options;
        }

        return $this->options;
    }
}
