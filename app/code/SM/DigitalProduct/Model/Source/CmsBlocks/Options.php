<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Helper\Block
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Model\Source\CmsBlocks;

use Magento\Cms\Model\Block;
use Magento\Cms\Model\ResourceModel\Block\Collection as BlockCollection;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory as BlockCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 * @package SM\DigitalProduct\Helper\Block
 */
class Options implements OptionSourceInterface
{
    /**
     * @var BlockCollectionFactory
     */
    protected $blockCollectionFactory;

    /**
     * Options constructor.
     * @param BlockCollectionFactory $blockCollectionFactory
     */
    public function __construct(
        BlockCollectionFactory $blockCollectionFactory
    ) {
        $this->blockCollectionFactory = $blockCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        /** @var BlockCollection $blockCollection */
        $blockCollection = $this->blockCollectionFactory->create();

        $options = [
            [
                "value" => '',
                "label" => __('------Please select a block------')
            ]
        ];
        /** @var Block $block */
        foreach ($blockCollection as $block) {
            $options[] = [
                "value" => $block->getIdentifier(),
                "label" => $block->getTitle()
            ];
        }
        return $options;
    }
}
