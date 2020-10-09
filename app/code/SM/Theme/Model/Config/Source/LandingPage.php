<?php
/**
 * @category SM
 * @package SM_Theme
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Chinhvd <chinhvd@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);

namespace SM\Theme\Model\Config\Source;

use Magento\Cms\Model\ResourceModel\Page\CollectionFactory;

class LandingPage implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Page collection factory
     *
     * @var CollectionFactory
     */
    protected $_pageCollectionFactory;

    /**
     * Construct
     *
     * @param CollectionFactory $pageCollectionFactory
     */
    public function __construct(CollectionFactory $pageCollectionFactory)
    {
        $this->_pageCollectionFactory = $pageCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $collection = $this->options = $this->_pageCollectionFactory->create();

            $this->options = [['label' => '', 'value' => '']];

            foreach ($collection as $page) {
                $this->options[] = [
                    'label' => __($page->getTitle()),
                    'value' => $page->getId()
                ];
            }
            array_unshift($this->options, ['value' => '', 'label' => __('Please select a cms page.')]);
        }
        return $this->options;
    }
}
