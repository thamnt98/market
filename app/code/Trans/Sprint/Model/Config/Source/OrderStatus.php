<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Orderstatus implements ArrayInterface
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Status\Collection
     */
    protected $statusCollection;

    /**
     * @param \Magento\Sales\Model\ResourceModel\Order\Status\Collection $statusCollection
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Status\Collection $statusCollection
    ) {
        $this->statusCollection = $statusCollection;
    }
   
    public function toOptionArray()
    {
        return $this->statusCollection->toOptionArray();
    }
}
