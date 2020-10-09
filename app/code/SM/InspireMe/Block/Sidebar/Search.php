<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Block\Sidebar;

/**
 * Class Search
 * @package SM\InspireMe\Block\Sidebar
 */
class Search extends \Mirasvit\Blog\Block\Sidebar\Search
{
    /**
     * Get Search Value
     * @return string
     */
    public function getSearchValue()
    {
        return $this->_request->getParam('q');
    }
}
