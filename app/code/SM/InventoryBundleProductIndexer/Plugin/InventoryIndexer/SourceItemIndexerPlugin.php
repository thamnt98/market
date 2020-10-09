<?php
/**
 * Class SourceItemIndexerPlugin
 * @package SM\InventoryBundleProductIndexer\Plugin\InventoryIndexer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\InventoryBundleProductIndexer\Plugin\InventoryIndexer;

use Magento\Framework\Exception\StateException;
use SM\InventoryBundleProductIndexer\Indexer\SourceItem\SourceItemIndexer as BundleProductsSourceItemIndexer;
use Magento\InventoryIndexer\Indexer\SourceItem\SourceItemIndexer;

class SourceItemIndexerPlugin
{
    /**
     * @var BundleProductsSourceItemIndexer
     */
    private $bundleProductsSourceItemIndexer;

    /**
     * @param BundleProductsSourceItemIndexer $bundleProductsSourceItemIndexer
     */
    public function __construct(
        BundleProductsSourceItemIndexer $bundleProductsSourceItemIndexer
    ) {
        $this->bundleProductsSourceItemIndexer = $bundleProductsSourceItemIndexer;
    }

    /**
     * @param SourceItemIndexer $subject
     * @param void $result
     * @param array $sourceItemIds
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws StateException
     * @throws \Exception
     */
    public function afterExecuteList(
        SourceItemIndexer $subject,
        $result,
        array $sourceItemIds
    ) {
        $this->bundleProductsSourceItemIndexer->executeList($sourceItemIds);
    }
}
