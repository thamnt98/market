<?php
namespace SM\MobileApi\Model\Data\Catalog\Product\Bundle;

use Magento\Framework\Model\AbstractExtensibleModel;
use SM\MobileApi\Api\Data\Catalog\Product\BundleProduct\ProductOptionsInterface;

/**
 * Class Options
 * @package SM\MobileApi\Model\Data\Catalog\Product\Bundle
 */
class Options extends AbstractExtensibleModel implements ProductOptionsInterface
{
    /**
     * @return mixed|null
     */
    public function getOptionId()
    {
        return $this->getData(self::OPTION_ID);
    }

    /**
     * @param int $data
     * @return mixed|Options
     */
    public function setOptionId($data)
    {
        return $this->setData(self::OPTION_ID, $data);
    }

    /**
     * @return int|mixed|null
     */
    public function getParentId()
    {
        return $this->getData(self::PARENT_ID);
    }

    /**
     * @param int $data
     * @return Options
     */
    public function setParentId($data)
    {
        return $this->setData(self::PARENT_ID, $data);
    }

    /**
     * @return mixed|null
     */
    public function getRequired()
    {
        return $this->getData(self::REQUIRED);
    }

    /**
     * @param $data
     * @return Options
     */
    public function setRequired($data)
    {
        return $this->setData(self::REQUIRED, $data);
    }

    /**
     * @return mixed|null
     */
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * @param $data
     * @return Options
     */
    public function setPosition($data)
    {
        return $this->setData(self::POSITION, $data);
    }

    /**
     * @return mixed|null
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @param $data
     * @return Options
     */
    public function setType($data)
    {
        return $this->setData(self::TYPE, $data);
    }

    /**
     * @return mixed|null
     */
    public function getDefaultTitle()
    {
        return $this->getData(self::DEFAULT_TITLE);
    }

    /**
     * @param $data
     * @return Options
     */
    public function setDefaultTitle($data)
    {
        return $this->setData(self::DEFAULT_TITLE, $data);
    }

    /**
     * @return mixed|null
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @param $data
     * @return Options
     */
    public function setTitle($data)
    {
        return $this->setData(self::TITLE, $data);
    }

    /**
     * @return mixed|null
     */
    public function getSelections()
    {
        return $this->getData(self::SELECTIONS);
    }

    /**
     * @param $data
     * @return Options
     */
    public function setSelections($data)
    {
        return $this->setData(self::SELECTIONS, $data);
    }
}
