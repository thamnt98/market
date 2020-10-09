<?php

namespace SM\MobileApi\Api\Data\Catalog\Product;

/**
 * Interface for storing image infomation
 */
interface ImageInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const URL = 'url';
    const LABEL = 'label';
    const TYPE = 'type';
    const VIDEO_URL = 'video_url';
    const IMAGES_360_URL = 'images_360_url';

    /**
     * Get Image Url
     *
     * @return string
     */
    public function getUrl();

    /**
     * Set Image Url
     *
     * @param $url
     *
     * @return $this
     */
    public function setUrl($url);

    /**
     * Get Image Label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Set Image Label
     *
     * @param $lable
     *
     * @return $this
     */
    public function setLabel($lable);

    /**
     * Get Image Type
     *
     * @return string
     */
    public function getType();

    /**
     * Set Image Type
     *
     * @param $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get Video Url
     *
     * @return string
     */
    public function getVideoUrl();

    /**
     * Set Video Url
     *
     * @param $videoUrl
     *
     * @return $this
     */
    public function setVideoUrl($videoUrl);

    /**
     * @return string
     */
    public function get360Url();

    /**
     * @param string $url360Image
     * @return $this
     */
    public function set360Url($url360Image);
}
