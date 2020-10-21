<?php


namespace SM\Category\Model\Data\Catalog;

use SM\Category\Api\Data\Catalog\CategoryMetaDataInterface;

/**
 * Class for storing category color
 * @package SM\Category\Model\Data\Catalog
 */
class CategoryMetaData extends \Magento\Framework\Model\AbstractExtensibleModel implements CategoryMetaDataInterface
{
    /**
     * @return int
     */
    public function getEntityId(){
       return $this->getData(self::ENTITY_ID);
    }

    /**
     * @param int $data
     * @return $this
     */
    public function setEntityId($data){
        return $this->setData(self::ENTITY_ID,$data);
    }

    /**
     * Get Gallery
     * @return \SM\HeroBanner\Api\Data\BannerInterface[]
     */
    public function getGallery(){
        return $this->getData(self::GALLERY);
    }

    /**
     * @param \SM\HeroBanner\Api\Data\BannerInterface[] $data
     * @return $this
     */
    public function setGallery($data){
        return $this->setData(self::GALLERY,$data);
    }

    /**
     * Get Color
     * @return \SM\Category\Api\Data\Catalog\CategoryColorInterface
     */
    public function getColor(){
        return $this->getData(self::COLOR);
    }

    /**
     * @param \SM\Category\Api\Data\Catalog\CategoryColorInterface $data
     * @return $this
     */
    public function setColor($data){
        return $this->setData(self::COLOR,$data);
    }

    /**
     * @inheritDoc
     */
    public function getIsTobacco()
    {
        return $this->getData(self::IS_TOBACCO) ?? false;
    }

    /**
     * @inheritDoc
     */
    public function getIsAlcohol()
    {
        return $this->getData(self::IS_ALCOHOL) ?? false;
    }

    /**
     * @inheritDoc
     */
    public function setIsTobacco($value)
    {
        return $this->setData(self::IS_TOBACCO, $value);
    }

    /**
     * @inheritDoc
     */
    public function setIsAlcohol($value)
    {
        return $this->setData(self::IS_ALCOHOL, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIsFresh()
    {
        return $this->getData(self::IS_FRESH);
    }

    /**
     * @inheritDoc
     */
    public function setIsFresh($value)
    {
        return $this->setData(self::IS_FRESH, $value);
    }
}
