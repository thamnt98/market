<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\MobileApi\Api\Data\Cms;

/**
 * Interface BlockContentInterface
 * @package SM\MobileApi\Api\Data\Cms
 */
interface BlockContentInterface
{
    const CONTENT = 'content';

    /**
     * @return string
     */
    public function getContent();

    /**
     * @param string $value
     * @return $this
     */
    public function setContent($value);
}
