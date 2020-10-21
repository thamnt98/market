<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: October, 15 2020
 * Time: 3:15 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model\Source\RedirectKey;

class Help implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \SM\Help\Model\ResourceModel\Topic\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var null|array
     */
    protected $options = null;

    /**
     * Order constructor.
     *
     * @param \SM\Help\Model\ResourceModel\Topic\CollectionFactory $collectionFactory
     */
    public function __construct(
        \SM\Help\Model\ResourceModel\Topic\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        if (is_null($this->options)) {
            $this->options = [];
            /** @var \SM\Help\Model\ResourceModel\Topic\Collection $coll */
            $coll = $this->collectionFactory->create();
            $coll->addVisibilityFilter()
                ->addFieldToFilter('store_id', ['eq' => 0]);

            /** @var \SM\Help\Model\Topic $topic */
            foreach ($coll->getItems() as $topic) {
                $this->options[] = [
                    'label' => $topic->getName(),
                    'value' => $topic->getId(),
                ];
            }
        }

        return $this->options;
    }
}
