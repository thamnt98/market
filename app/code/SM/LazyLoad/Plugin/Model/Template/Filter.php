<?php
/**
 * Class Filter
 * @package SM\LazyLoad\Plugin\Model\Template
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */


namespace SM\LazyLoad\Plugin\Model\Template;


class Filter
{
    /**
     * @var \SM\LazyLoad\Helper\Filter
     */
    protected $filterHelper;

    /**
     * @param \SM\LazyLoad\Helper\Filter $filterHelper
     */
    public function __construct(
        \SM\LazyLoad\Helper\Filter $filterHelper
    ) {
        $this->filterHelper = $filterHelper;
    }

    /**
     * @param \Magento\Cms\Model\Template\Filter $filter
     * @param $result
     * @return mixed|string
     */
    public function afterFilter(
        \Magento\Cms\Model\Template\Filter $filter,
        $result
    ) {
        if (is_string($result)) {
            $result = $this->filterHelper->filter($result);
        }
        return $result;
    }
}
