<?php
/**
 * @category SM
 * @package SM_Theme
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Chinhvd <chinhvd@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Theme\plugin;


/**
 * Container for the configuration related to the widget initializer mechanism
 */
class WidgetInitializerConfig
{
   public function afterGetConfig(\Magento\PageBuilder\Model\WidgetInitializerConfig $subject,$result)
   {
       if (isset($result["[data-content-type=\"slider\"][data-appearance=\"default\"]"])){
           $result["[data-content-type=\"slider\"]"] = $result["[data-content-type=\"slider\"][data-appearance=\"default\"]"];
           unset($result["[data-content-type=\"slider\"][data-appearance=\"default\"]"]);
       }
       return $result;
   }
}
