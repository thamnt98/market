<?php
/**
 * Class SearchResults
 * @package SM\Help\Model
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Help\Model;

use SM\Help\Api\Data\SearchResultsInterface;

/**
 * Service Data Object with Question search results.
 */
class SearchResults extends \Magento\Framework\Api\SearchResults implements SearchResultsInterface
{
}
