<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Model\Data;

use Magento\Framework\Model\AbstractModel;
use SM\InspireMe\Api\Data\PinDetailInterface;

/**
 * Class PinDetail
 * @package SM\InspireMe\Model\Data
 */
class PinDetail extends AbstractModel implements \SM\InspireMe\Api\Data\PinDetailInterface
{
    /**
     * @return float
     */
    public function getTop()
    {
        return $this->getData(self::TOP);
    }

    /**
     * @return float
     */
    public function getLeft()
    {
        return $this->getData(self::LEFT);
    }

    /**
     * @inheritDoc
     */
    public function getWidth()
    {
        return $this->getData(self::WIDTH);
    }

    /**
     * @inheritDoc
     */
    public function getHeight()
    {
        return $this->getData(self::HEIGHT);
    }

    /**
     * @inheritDoc
     */
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * @inheritDoc
     */
    public function getCustomText()
    {
        return $this->getData(self::CUSTOM_TEXT);
    }

    /**
     * @inheritDoc
     */
    public function getImgH()
    {
        return $this->getData(self::IMG_H);
    }

    /**
     * @inheritDoc
     */
    public function getImgW()
    {
        return $this->getData(self::IMG_W);
    }

    /**
     * @inheritDoc
     */
    public function getTopPercent()
    {
        return (float)$this->getTop() * 100 / $this->getImgH();
    }

    /**
     * @inheritDoc
     */
    public function getLeftPercent()
    {
        return (float)$this->getLeft() * 100 / $this->getImgW();
    }

    /**
     * @inheritDoc
     */
    public function setProduct($value)
    {
        return $this->setData(self::PRODUCT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getProduct()
    {
        return $this->getData(self::PRODUCT);
    }
}
