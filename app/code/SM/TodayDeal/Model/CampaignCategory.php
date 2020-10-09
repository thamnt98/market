<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\TodayDeal\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class CampaignCategory
 * @package SM\TodayDeal\Model
 */
class CampaignCategory extends AbstractModel implements \SM\TodayDeal\Api\Data\CampaignCategoryInterface
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }
}
