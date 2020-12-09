<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: December, 07 2020
 * Time: 3:42 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model\Source\Config;

class Help implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \SM\Help\Model\ResourceModel\Topic\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Help constructor.
     *
     * @param \Magento\Framework\App\RequestInterface              $request
     * @param \SM\Help\Model\ResourceModel\Topic\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \SM\Help\Model\ResourceModel\Topic\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->request = $request;
    }

    public function toOptionArray()
    {
        if (!$this->options) {
            /** @var \SM\Help\Model\ResourceModel\Topic\Collection $coll */
            $coll = $this->collectionFactory->create();
            $coll->addFieldToFilter('store_id', $this->request->getParam('store', 0));

            $this->options[] = [
                'label' => __('-- Select --'),
                'value' => ''
            ];
            foreach ($coll->getItems() as $item) {
                $this->options[] = [
                    'label' => $item->getData('name'),
                    'value' => $item->getData('topic_id'),
                ];
            }
        }

        return $this->options;
    }
}
