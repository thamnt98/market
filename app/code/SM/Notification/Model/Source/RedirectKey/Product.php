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

use Magento\Catalog\Model\Product\Attribute\Source\Status as Status;
use Magento\Catalog\Model\Product\Visibility;

class Product implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var null|array
     */
    protected $options = null;

    /**
     * Order constructor.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        if (is_null($this->options)) {
            $this->options = [];
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $coll */
            $coll = $this->collectionFactory->create();
            $coll->addAttributeToFilter('status', Status::STATUS_ENABLED)
                ->setVisibility([Visibility::VISIBILITY_IN_CATALOG, Visibility::VISIBILITY_BOTH]);

            /** @var \Magento\Catalog\Model\Product $product */
            foreach ($coll->getItems() as $product) {
                $this->options[] = [
                    'label' => $product->getSku(),
                    'value' => $product->getSku(),
                ];
            }
        }

        return $this->options;
    }
}
