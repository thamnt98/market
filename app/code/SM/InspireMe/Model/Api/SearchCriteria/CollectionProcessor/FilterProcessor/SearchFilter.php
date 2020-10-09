<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor;

use Magento\Framework\Api\Filter;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class SearchFilter
 * @package SM\InspireMe\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor
 */
class SearchFilter implements \Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface
{
    /**
     * Apply search Filter to Article Collection
     *
     * @param Filter $filter
     * @param AbstractDb $collection
     * @return bool Whether the filter is applied
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply(Filter $filter, AbstractDb $collection)
    {
        $value = $filter->getValue();

        /** @var \Mirasvit\Blog\Model\ResourceModel\Post\Collection $collection */
        $collection->addSearchFilter($value);

        return true;
    }
}
