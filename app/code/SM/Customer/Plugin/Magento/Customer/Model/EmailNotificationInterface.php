<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Customer\Plugin\Magento\Customer\Model;

/**
 * Class EmailNotificationInterface
 * @package SM\Customer\Plugin\Magento\Customer\Model
 */
class EmailNotificationInterface
{
    /**
     * @param \Magento\Customer\Model\EmailNotificationInterface $subject
     * @param \Closure $proceed
     * @return \Magento\Customer\Model\EmailNotificationInterface
     */
    public function aroundNewAccount(
        \Magento\Customer\Model\EmailNotificationInterface $subject,
        \Closure $proceed
    ) {
        return $subject;
    }
}
