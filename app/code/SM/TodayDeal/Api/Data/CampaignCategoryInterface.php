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
 * Interface CampaignCategoryInterface
 * @package SM\TodayDeal\Api\Data
 */
interface CampaignCategoryInterface
{
    const ID = 'id';
    const NAME = 'name';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();
}
