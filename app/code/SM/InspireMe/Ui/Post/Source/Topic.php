<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Ui\Post\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Topic
 * @package SM\InspireMe\Ui\Post\Source
 */
class Topic implements OptionSourceInterface
{
    /**
     * @var \Mirasvit\Blog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Topic constructor.
     * @param \Mirasvit\Blog\Model\ResourceModel\Category\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Mirasvit\Blog\Model\ResourceModel\Category\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        /** @var \Mirasvit\Blog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->collectionFactory->create()
            ->addAttributeToSelect('*')
            ->excludeRoot();

        $result = [];

        /** @var \Mirasvit\Blog\Model\Category $item */
        foreach ($collection as $item) {
            $result[] = [
                'value' => $item->getId(),
                'label' => $item->getName(),
            ];
        }

        return $result;
    }
}
