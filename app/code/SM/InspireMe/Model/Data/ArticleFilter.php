<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Model\Data;

use Magento\Framework\Model\AbstractModel;

/**
 * Class ArticleFilter
 * @package SM\InspireMe\Model\Data
 */
class ArticleFilter extends AbstractModel implements \SM\InspireMe\Api\Data\ArticleFilterInterface
{
    /**
     * @inheritDoc
     */
    public function getArticles()
    {
        return $this->getData(self::ARTICLES);
    }

    /**
     * @inheritDoc
     */
    public function setArticles($value)
    {
        return $this->setData(self::ARTICLES, $value);
    }

    /**
     * @inheritDoc
     */
    public function getFilters()
    {
        return $this->getData(self::FILTERS);
    }

    /**
     * @inheritDoc
     */
    public function setFilters($value)
    {
        return $this->setData(self::FILTERS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOrders()
    {
        return $this->getData(self::ORDERS);
    }

    /**
     * @inheritDoc
     */
    public function setOrders($value)
    {
        return $this->setData(self::ORDERS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDirections()
    {
        return $this->getData(self::DIRECTIONS);
    }

    /**
     * @inheritDoc
     */
    public function setDirections($value)
    {
        return $this->setData(self::DIRECTIONS, $value);
    }
}
