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

class Order implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var null|array
     */
    protected $options = null;

    /**
     * Order constructor.
     *
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        if (is_null($this->options)) {
            $this->options = [];
            $coll = $this->collectionFactory->create();
            $coll->addFieldToFilter('is_parent', 1);

            foreach ($coll->getItems() as $order) {
                $this->options[] = [
                    'label' => '#' . $order->getIncrementId(),
                    'value' => $order->getEntityId(),
                ];
            }
        }

        return $this->options;
    }
}
