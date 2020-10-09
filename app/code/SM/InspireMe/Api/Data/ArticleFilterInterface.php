<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Api\Data;

/**
 * Interface ArticleFilterInterface
 * @package SM\InspireMe\Api\Data
 */
interface ArticleFilterInterface
{
    const ARTICLES   = 'articles';
    const FILTERS    = 'filters';
    const ORDERS     = 'orders';
    const DIRECTIONS = 'directions';

    /**
     * @return \SM\InspireMe\Api\Data\PostListingInterface[]
     */
    public function getArticles();

    /**
     * @param \SM\InspireMe\Api\Data\PostListingInterface[] $value
     * @return $this
     */
    public function setArticles($value);

    /**
     * @return \Mirasvit\Blog\Api\Data\CategoryInterface[]
     */
    public function getFilters();

    /**
     * @param \Mirasvit\Blog\Api\Data\CategoryInterface[] $value
     * @return $this
     */
    public function setFilters($value);

    /**
     * @return string[]
     */
    public function getOrders();

    /**
     * @param string[] $value
     * @return $this
     */
    public function setOrders($value);

    /**
     * @return string[]
     */
    public function getDirections();

    /**
     * @param string[] $value
     * @return $this
     */
    public function setDirections($value);
}
