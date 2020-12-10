<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\LayeredNavigation\Model\Webapi\AmastyShopby\Model\Layer\Filter;

/**
 * Class Price
 * @package SM\LayeredNavigation\Model\Webapi\AmastyShopby\Model\Layer\Filter
 */
class Price extends \SM\CustomPrice\Model\Layer\Filter\Price
{
    /**
     * @param float|string $fromPrice
     * @param float|string $toPrice
     * @return float|\Magento\Framework\Phrase|string
     */
    protected function _renderRangeLabel($fromPrice, $toPrice)
    {
        $result = parent::_renderRangeLabel($fromPrice, $toPrice);
        return strip_tags($result);
    }
}
