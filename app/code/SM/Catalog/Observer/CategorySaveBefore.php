<?php
/**
 * Class CategorySaveBefore
 * @package SM\Catalog\Observer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Catalog\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use SM\Catalog\Setup\Patch\Data\AddHideCTAttribute;

class CategorySaveBefore implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * CategorySaveBefore constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /**
         * @var \Magento\Catalog\Model\Category $category
         */
        $category = $observer->getEvent()->getDataObject();
        if ($category->dataHasChangedFor(AddHideCTAttribute::CTA_ATTRIBUTE)) {
            $category->setData('is_changed_cta', '1');
            if (!$this->scopeConfig->isSetFlag('trans_catalog/product/update_cta_cron')) {
                $category->setData('is_changed_cta', '0');
            }
        }
    }
}
