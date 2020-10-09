<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\MobileApi\Api;

/**
 * Interface CmsManagementInterface
 * @package SM\MobileApi\Api
 */
interface CmsManagementInterface
{
    const TERMS_CONDITIONS_URL = 'mobile_api/index/termsconditions';
    const PRIVACY_POLICY_URL = 'mobile_api/index/privacypolicy';
    const ABOUT_US = 'mobile_api/index/aboutus';

    /**
     * Get CMS Page URL
     *
     * @return string[]
     */
    public function getCmsPages();
}
