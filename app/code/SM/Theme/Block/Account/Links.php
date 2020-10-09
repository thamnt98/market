<?php
/**
 * @category SM
 * @package SM_Theme
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      DucNH2 <ducnh2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);

namespace SM\Theme\Block\Account;

use Magento\Customer\Block\Account\SortLinkInterface;

/**
 * Class Links
 * @package SM\Theme\Block\Account
 */
class Links extends \Magento\Framework\View\Element\Html\Link implements SortLinkInterface
{
    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }
}
