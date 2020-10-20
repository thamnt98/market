<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\TodayDeal\Api\Data;

/**
 * Interface MenuDealListingMobileInterface
 * @package SM\TodayDeal\Api\Data
 */
interface MenuDealListingMobileInterface
{
    const ID = 'id';
    const TYPE = 'type';
    const TITLE = 'title';

    const TYPE_ALL = 0;
    const TYPE_SURPRISE_DEAL = 1;
    const TYPE_CAMPAIGN = 2;

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $value
     * @return $this
     */
    public function setId($value);

    /**
     * @return int
     */
    public function getType();

    /**
     * @param int $value
     * @return $this
     */
    public function setType($value);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $value
     * @return $this
     */
    public function setTitle($value);
}
