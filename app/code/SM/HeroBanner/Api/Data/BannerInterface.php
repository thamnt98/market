<?php

namespace SM\HeroBanner\Api\Data;

interface BannerInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * @return integer
     */
    public function getNewtab();

    /**
     * @param $data
     * @return $this
     */
    public function setNewtab($data);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param $data
     * @return $this
     */
    public function setUrl($data);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @param $data
     * @return $this
     */
    public function setImage($data);

    /**
     * @param $data
     * @return $this
     */
    public function setName($data);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $data
     * @return $this
     */
    public function setTitle($data);

    /**
     * @return string
     */
    public function getSubTitle();

    /**
     * @param string $data
     * @return $this
     */
    public function setSubTitle($data);

    /**
     * @return string
     */
    public function getContent();

    /**
     * @param string $content
     * @return $this
     */
    public function setContent($content);

    /**
     * @return integer
     */
    public function getCategoryId();

    /**
     * @param $data
     * @return $this
     */
    public function setCategoryId($data);

    /**
     * @return int
     */
    public function getPromoId();

    /**
     * @param $data
     * @return $this
     */
    public function setPromoId($data);

    /**
     * @return string
     */
    public function getPromoName();

    /**
     * @param string $data
     * @return $this
     */
    public function setPromoName($data);

    /**
     * @return string
     */
    public function getPromoCreative();

    /**
     * @param string $data
     * @return $this
     */
    public function setPromoCreative($data);

    /**
     * @return int
     */
    public function getPromoPosition();

    /**
     * @param int $data
     * @return $this
     */
    public function setPromoPosition($data);

    /**
     * @return int
     */
    public function getLinkType();

    /**
     * @param int $value
     * @return $this
     */
    public function setLinkType($value);

    /**
     * @return string
     */
    public function getLinkTypeValue();

    /**
     * @param string $value
     * @return $this
     */
    public function setLinkTypeValue($value);
}
