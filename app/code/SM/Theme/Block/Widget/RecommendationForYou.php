<?php
/**
 * SM\Theme\Block\Widget
 *
 * @copyright Copyright © 2021 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\Theme\Block\Widget;

use Magento\Widget\Block\BlockInterface;
use SM\Reports\Block\Product\LatestViewed;

/**
 * Class RecommendationForYou
 * @package SM\Theme\Block\Widget
 */
class RecommendationForYou extends LatestViewed implements BlockInterface
{
    protected $_template = "widget/recommendation_for_you.phtml";
}
