<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Api\Data;

/**
 * Interface LookbookDetailInterface
 * @package SM\InspireMe\Api\Data
 */
interface LookbookDetailInterface
{
    const ID     = 'lookbook_id';
    const STATUS = 'status';
    const NAME   = 'name';
    const IMAGE  = 'image';
    const PINS   = 'pins';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getImageUrl();

    /**
     * @return \SM\InspireMe\Api\Data\PinDetailInterface[]
     */
    public function getPins();
}
