<?php
/**
 * Class ${NAME}
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Help\Api\Data;

interface SearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get Question list.
     *
     * @return \SM\Help\Api\Data\SearchQuestionInterface[]
     */
    public function getItems();

    /**
     * Set Question list.
     *
     * @param \SM\Help\Api\Data\SearchQuestionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
