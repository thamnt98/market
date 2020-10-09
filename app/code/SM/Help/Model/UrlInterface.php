<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Model;

/**
 * Interface UrlInterface
 * @package SM\Help\Model
 */
interface UrlInterface
{
    /**
     * @param array $urlParams
     * @return string
     */
    public function getUrl($urlParams = []);
}
