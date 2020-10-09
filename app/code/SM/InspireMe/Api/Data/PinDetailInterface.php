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
 * Interface PinDetailInterface
 * @package SM\InspireMe\Api\Data
 */
interface PinDetailInterface
{
    const ID = 'id';
    const TOP = 'top';
    const LEFT = 'left';
    const WIDTH = 'width';
    const HEIGHT = 'height';
    const TEXT = 'text';
    const POSITION = 'position';
    const CUSTOM_TEXT = 'customer_text';
    const IMG_H = 'imgH';
    const IMG_W = 'imgW';
    const PRODUCT = 'product';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getWidth();

    /**
     * @return int
     */
    public function getHeight();

    /**
     * @return float
     */
    public function getTopPercent();

    /**
     * @return float
     */
    public function getLeftPercent();

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @return string
     */
    public function getCustomText();

    /**
     * @return int
     */
    public function getImgH();

    /**
     * @return int
     */
    public function getImgW();

    /**
     * @param \SM\MobileApi\Api\Data\Product\ListItemInterface $value
     * @return $this
     */
    public function setProduct($value);

    /**
     * @return \SM\MobileApi\Api\Data\Product\ListItemInterface
     */
    public function getProduct();
}
