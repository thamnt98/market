<?php

namespace SM\MobileApi\Api\Data\Slider;

interface HomeSliderInterface
{
    const IMAGE_URL     = 'image_url';
    const DESCRIPTION   = 'description';

    /**
     * @return string
     */
    public function getImageUrl();

    /**
     * @param string $imageUrl
     * @return $this
     */
    public function setImageUrl($imageUrl);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description);
}
