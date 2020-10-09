<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\MobileApi\Model\Data\Cms;

use Magento\Framework\Model\AbstractModel;

/**
 * Class BlockContent
 * @package SM\MobileApi\Model\Data\Cms
 */
class BlockContent extends AbstractModel implements \SM\MobileApi\Api\Data\Cms\BlockContentInterface
{
    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * @inheritDoc
     */
    public function setContent($value)
    {
        return $this->setData(self::CONTENT, $value);
    }
}
